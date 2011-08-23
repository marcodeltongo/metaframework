<?php

/**
 * ActiveRecord is the customized base active record class.
 *
 * All active record classes for this application should extend from this base class.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class ActiveRecord extends CActiveRecord
{

	/**
	 * The attributes copy
	 *
	 * @var array
	 */
	public $__oldAttributes = array();

	/**
	 * Sets the attribute and related values in a massive way.
	 *
	 * @param array $values attribute values (name=>value) to be set.
	 * @param boolean $safeOnly whether the assignments should only be done to the safe attributes.
	 */
	public function setAttributes($values, $safeOnly = true)
	{
		if (!is_array($values) or empty($values)) {
			return;
		}
		/*
		 * Prepare attributes and relations informations.
		 */
		$attributes = array_flip($safeOnly ? $this->getSafeAttributeNames() : $this->attributeNames());
		$relations = $this->getMetaData()->relations;
		/*
		 * Check empty relations from HTML select.
		 */
		if (isset($values['__relations'])) {
			foreach ($values['__relations'] as $rel) {
				if (!isset($values[$rel])) {
					$this->$rel = array();
				}
			}
			unset($values['__relations']);
		}
		/*
		 * Save data
		 */
		foreach ($values as $name => $value) {
			if (isset($attributes[$name]) or isset($relations[$name])) {
				$this->$name = $value;
			} elseif ($safeOnly) {
				$this->onUnsafeAttribute($name, $value);
			}
		}
	}

	/**
	 * Save related records.
	 *
	 * @param string $relationName
	 * @param boolean $runValidation
	 *
	 * @return boolean
	 */
	public function saveRelation($relationName, $runValidation = true)
	{
		/*
		 * We need a saved object to have his PK.
		 */
		if ($this->getIsNewRecord()) {
			throw new CException(__METHOD__ . " cannot be used for new objects.");
		}
		/*
		 * Check if we know how to manage relation.
		 */
		$relation = $this->getActiveRelation($relationName);
		if (null === $relation) {
			throw new CException("Relation '$relationName' unknown.");
		}
		/*
		 * Manage depending on relation type.
		 */
		switch (get_class($relation)) {
			/*
			 * Check ManyMany first because it extends from HasMany.
			 */
			case 'CManyManyRelation':
				$model = new $relation->className;
				/*
				 * Junction table
				 */
				if (!preg_match('/^\s*\{{0,2}\s*(.+?)\s*\}{0,2}\s*\(\s*(.+)\s*,\s*(.+)\s*\)\s*$/s', $relation->foreignKey, $matches)) {
					throw new CException("Unable to get table and foreign key information from MANY_MANY relation definition (" . $relation->foreignKey . ")");
				}
				list($match, $junctionTable, $junctionLFK, $junctionRFK) = $matches;
				/*
				 * Find all models that can be related
				 */
				$availableRows = $model->findAll(new CDbCriteria(array('index' => $model->getMetaData()->tableSchema->primaryKey)));
				/*
				 * Remove all to ease logic.
				 */
				$criteria = new CDbCriteria;
				$criteria->compare($junctionLFK, $this->primaryKey);
				$this->getCommandBuilder()->createDeleteCommand($junctionTable, $criteria)->execute();
				/*
				 * Ensure relation value is an array (of PKs or objects).
				 */
				$this->$relationName = (is_scalar($this->$relationName)) ? array($this->$relationName) : $this->$relationName;
				/*
				 * Save records
				 */
				foreach ($this->$relationName as $id) {
					if (is_object($id)) {
						$id = $id->primaryKey;
					}
					if (array_key_exists($id, $availableRows)) {
						/*
						 * CDbCommand throws an exception on error.
						 */
						$this->getCommandBuilder()->createInsertCommand($junctionTable, array($junctionLFK => $this->primaryKey, $junctionRFK => $id))->execute();
						unset($availableRows[$id]);
					}
				}

				break;

			/*
			 * HasMany
			 */
			case 'CHasManyRelation':
				$model = new $relation->className;
				/*
				 * Prepare related PKs arrays.
				 */
				$items = array();
				foreach ($this->$relationName as $item) {
					$items[] = (is_object($item)) ? $item->primaryKey : $item;
				}
				/*
				 * Remove foreignKey from unselected records.
				 */
				$criteria = new CDbCriteria;
				$criteria->addColumnCondition(array($relation->foreignKey => $this->primaryKey));
				$criteria->addNotInCondition($model->getMetaData()->tableSchema->primaryKey, $items);
				$class = $relation->className;
				$class::model()->updateAll(array($relation->foreignKey => null), $criteria);
				/*
				 * Update foreignKey in selected records.
				 */
				$criteria = new CDbCriteria;
				$criteria->addInCondition($model->getMetaData()->tableSchema->primaryKey, $items);
				$class = $relation->className;
				$class::model()->updateAll(array($relation->foreignKey => $this->primaryKey), $criteria);

				break;
		}
		/*
		 * Cleanup to let Yii refresh data
		 */
		unset($this->$relationName);

		return true;
	}

	/**
	 * Override CActiveRecord save to add transactions and relational save.
	 *
	 * @param boolean $runValidation
	 * @param array $attributes
	 * @param boolean $saveRelated
	 *
	 * @return boolean
	 */
	public function save($runValidation = true, $attributes = null, $saveRelated = true)
	{
		/*
		 * Start a new transaction if none running.
		 */
		$transaction = null;
		if (null === $this->dbConnection->getCurrentTransaction()) {
			$transaction = $this->dbConnection->beginTransaction();
		}
		/*
		 * Merge passed attributes.
		 */
		if (!empty($attributes)) {
			$this->setAttributes($attributes, false);
		}
		/*
		 * Set empty strings to null
		 */
		foreach (array_keys($this->attributes) as $attributeName) {
			if (is_string($this->$attributeName) and trim($this->$attributeName) == '') {
				$this->$attributeName = null;
			}
		}
		/*
		 * Save
		 */
		if (parent::save($runValidation)) {
			/*
			 * Save related
			 */
			foreach ($this->getMetaData()->relations as $relation => $relation_info) {
				if ($saveRelated and !$this->saveRelation($relation, $runValidation)) {
					/*
					 * Rollback only if we started the transaction
					 */
					if (null !== $transaction) {
						$transaction->rollBack();
					}
					return false;
				}
			}
			/*
			 * Commit only if we started the transaction
			 */
			if (null !== $transaction) {
				$transaction->commit();
			}
			return true;
		}
		/*
		 * Rollback only if we started the transaction
		 */
		if (null !== $transaction) {
			$transaction->rollBack();
		}
		return false;
	}

	/**
	 * Responds to {@link CModel::onAfterSave} event.
	 *
	 * @param CModelEvent event
	 */
	protected function afterSave()
	{
		parent::afterSave();
		$this->__oldAttributes = $this->attributes;
	}

	/**
	 * Responds to {@link CModel::onAfterDelete} event.
	 *
	 * @param CModelEvent event
	 */
	protected function afterDelete()
	{
		parent::afterDelete();
	}

	/**
	 * Responds to {@link CModel::onAfterFind} event.
	 *
	 * @param CModelEvent event
	 */
	protected function afterFind()
	{
		parent::afterFind();
		$this->__oldAttributes = $this->attributes;
	}

	/**
	 * Checks if attributes changed since last action.
	 *
	 * @return boolean
	 */
	public function changed()
	{
		foreach ($this->attributes as $key => $value) {
			if (!array_key_exists($key, $this->__oldAttributes)
					or ($value !== $this->__oldAttributes[$key])) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns a string representation of this model.
	 *
	 * Override in child classes to better suit your needs.
	 *
	 * @return string
	 */
	public function toString()
	{
		foreach ($this->attributes as $key => $value) {
			if (in_array($key, array('shortname', 'name', 'title'))) {
				return $value;
			}
		}
		return $this->getPrimaryKey();
	}

	/**
	 * Magic method
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Retrieves the list of associated behaviors.
	 *
	 * @return array associated behaviors.
	 */
	public function behaviors()
	{
		$behaviors = parent::behaviors();

		$behaviors['JsonBehavior'] = array(
			'class' => 'common.behaviors.JsonBehavior',
		);
		/*
		  $behaviors['ActiveRecordLogBehavior'] = array(
		  'class' => 'common.behaviors.ActiveRecordLogBehavior',
		  );
		 */
		return $behaviors;
	}

	/**
	 * Retrieves the list of customized attribute titles
	 *
	 * @return array customized attribute labels
	 */
	public function attributeTitles()
	{
		return array(
		);
	}

	/**
	 * Retrieves a customized attribute title
	 *
	 * @return string customized attribute title
	 */
	public function attributeTitle($name)
	{
		$titles = $this->attributeTitles();

		return (isset($titles[$name])) ? $titles[$name] : '';
	}

}
