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
         * Prepare data
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
     * @param array $elements
     * @param boolean $append
     *
     * @return boolean
     */
    public function saveRelated($relationName, $runValidation = true, $elements = array(), $append = false)
    {
        if ($this->getIsNewRecord()) {
            throw new CException("Function saveRelated() cannot be used for new objects, use saveWithRelated instead.");
        }

        $relation = $this->getActiveRelation($relationName);
        if (null === $relation) {
            throw new CException("Relation '$relationName' not found.");
        }

        $data = CMap::mergeArray($this->$relationName, $elements);

        switch (get_class($relation)) {

            case 'CManyManyRelation':
                $model = new $relation->className;
                /*
                 * Junction table
                 */
                if (!preg_match('/^\s*\{{0,2}\s*(.+?)\s*\}{0,2}\s*\(\s*(.+)\s*,\s*(.+)\s*\)\s*$/s', $relation->foreignKey, $matches)) {
                    throw new CException("Unable to get table and foreign key information from MANY_MANY relation definition (" . $relation->foreignKey . ")");
                }
                list($match, $junctionTable, $junctionLFK, $junctionRFK) = $matches;
                $junctionClass = (class_exists($junctionTable, false)) ? $junctionTable : false;
                /*
                 * Find all models that can be related
                 */
                $availableRows = $model->findAll(new CDbCriteria(array('index' => $model->getMetaData()->tableSchema->primaryKey)));
                if (!$append) {
                    $criteria = new CDbCriteria;
                    $criteria->compare($junctionLFK, $this->primaryKey);
                    $this->getCommandBuilder()->createDeleteCommand($junctionTable, $criteria)->execute();
                }
                /*
                 * Save records
                 */
                foreach ($data as $id) {
                    if (is_object($id)) {
                        $id = $id->primaryKey;
                    }
                    if (array_key_exists($id, $availableRows)) {
                        if ($junctionClass) {
                            $obj = new $junctionClass;
                            $obj->$junctionLFK = $this->primaryKey;
                            $obj->$junctionRFK = $id;
                            if (!$obj->save(true, null, false)) {
                                /*
                                 * Oh no, something went wrong.
                                 */
                                foreach ($obj->getErrors() as $errorOn => $errorMsgs) {
                                    $relabel = $this->getAttributeLabel($relationName);
                                    foreach ($errorMsgs as $errorMsg) {
                                        $errorMsg = Yii::t('yii', 'Related "{relation}": {message}', array('{relation}' => $relabel, '{message}' => $errorMsg));
                                        $this->addError($relationName, $errorMsg);
                                    }
                                }
                                return false;
                            }
                        } else {
                            /*
                             * CDbCommand throws an exception on error.
                             */
                            $this->getCommandBuilder()->createInsertCommand($junctionTable, array($junctionLFK => $this->primaryKey, $junctionRFK => $id))->execute();
                        }
                        unset($availableRows[$id]);
                    }
                }
                /*
                 * Cleanup to let Yii refresh data
                 */
                unset($this->$relationName);
                break;

            case 'CHasManyRelation':
                if (!$append) {
                    $class = $relation->className;
                    $class::model()->deleteAllByAttributes(array($relation->foreignKey => $this->primaryKey));
                }
                $dataProcessed = array();
                foreach ($data as $key => $value) {
                    $obj = new $relation->className;
                    $obj->attributes = ($value instanceof CActiveRecord) ? $value->attributes : $value;
                    $obj->{$relation->foreignKey} = $this->primaryKey;
                    if (!$obj->save(true, null, false)) {
                        /*
                         * Oh no, something went wrong.
                         */
                        foreach ($obj->getErrors() as $errorOn => $errorMsgs) {
                            $relabel = $this->getAttributeLabel($relationName);
                            foreach ($errorMsgs as $errorMsg) {
                                $errorMsg = Yii::t('yii', 'Related "{relation}": {message}', array('{relation}' => $relabel, '{message}' => $errorMsg));
                                $this->addError($relationName, $errorMsg);
                            }
                        }
                        return false;
                    }
                    $dataProcessed[$key] = $obj;
                }
                /*
                 * Cleanup to let Yii refresh data
                 */
                $this->$relationName = $dataProcessed;
                break;
        }

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
         * Start a new transaction if none running
         */
        $transaction = null;
        if (null === $this->dbConnection->getCurrentTransaction()) {
            $transaction = $this->dbConnection->beginTransaction();
        }

        /*
         * Save
         */
        if (parent::save($runValidation, $attributes)) {
            /*
             * Save related
             */
            foreach ($this->getMetaData()->relations as $relation => $relation_info) {
                $elements = (isset($attributes[$relation])) ? $attributes[$relation] : array();
                if ($saveRelated and !$this->saveRelated($relation, $runValidation, $elements, false)) {
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

        $behaviors['ActiveRecordLogBehavior'] = array(
                'class' => 'common.behaviors.ActiveRecordLogBehavior',
        );

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
