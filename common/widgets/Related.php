<?php

/**
 * Related widget class file.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class Related extends CWidget
{
    /**
     * Parent form
     *
     * @var CActiveForm
     */
    public $form;
    /**
     * Model instance
     *
     * @var ActiveRecord
     */
    public $model;
    /**
     * Model field
     *
     * @var string
     */
    public $field;
    /**
     * Relation to manage
     *
     * @var CActiveRelation
     */
    public $relation;
    /**
     * Relation key field
     *
     * @var string
     */
    public $relatedField;
    /**
     * Relation field(s) to use as shown text
     *
     * @var mixed
     */
    public $show = null;
    /**
     * Delimiter to use if $show is an array
     *
     * @var string
     */
    public $separator = ' | ';
    /**
     * HTML options passed to tag
     *
     * @var array
     */
    public $htmlOptions = array();
    /**
     * Related model instance
     *
     * @var ActiveRecord
     */
    protected $related;
    /**
     * Rows actually related to model
     *
     * @var ActiveRecord
     */
    protected $relatedRows;
    /**
     * Selected values in form
     *
     * @var array
     */
    protected $selectedRows;
    /**
     * Rows that can be related to model
     *
     * @var ActiveRecord
     */
    protected $availableRows;

    /**
     * Check parameters and try auto-detection.
     * Called by CController::beginWidget()
     */
    public function init()
    {
        /*
         * Check form
         */
        if (!($this->form instanceof CActiveForm)) {
            throw new CException('Model must be an instance of CActiveForm.');
        }
        /*
         * Check model
         */
        if (!($this->model instanceof CActiveRecord)) {
            throw new CException('Model must be an instance of CActiveRecord.');
        }
        /*
         * Check and set relation
         */
        if (null === $this->model->getActiveRelation($this->relation)) {
            throw new CException("Relation '{$this->relation}' not found in model definition.");
        }
        $this->relation = $this->model->getActiveRelation($this->relation);
        /*
         * Check and set model field
         */
        if (null === $this->field) {
            $this->field = $this->relation->foreignKey;
        }
        if (false !== strpos($this->field, '(') or !$this->model->hasAttribute($this->field)) {
            $this->field = $this->model->getMetaData()->tableSchema->primaryKey;
        }
        if (is_array($this->field) or !is_string($this->field)) {
            throw new CException("Composite foreign key not supported: " . var_export($this->field, true));
        }
        if (!$this->model->hasAttribute($this->field)) {
            throw new CException("Foreign key '{$this->field}' not found in model definition.");
        }
        /*
         * Related instance
         */
        $this->related = new $this->relation->className;
        if (null === $this->related) {
            throw new CException("Can't instantiate related '{$this->relation->className}' model.");
        }
        /*
         * Relation key field
         */
        if (null === $this->relatedField) {
            $this->relatedField = $this->related->getMetaData()->tableSchema->primaryKey;
        }
        if (is_array($this->relatedField)) {
            throw new CException("Related composite key not supported: " . var_export($this->relatedField, true));
        }
        if (!is_string($this->relatedField) or !$this->related->hasAttribute($this->relatedField)) {
            throw new CException("Related key not found in related model definition.");
        }
        /*
         * Check and set related model shown field(s)
         */
        if (null === $this->show) {
            $show_fields = array('title', 'name', 'username', 'description');
            foreach ($show_fields as $f) {
                if ($this->related->hasAttribute($f)) {
                    $this->show[] = $f;
                    break;
                }
            }
            if (null === $this->show) {
                $this->show = $this->related->getMetaData()->tableSchema->primaryKey;
            }
            if (is_array($this->show) and count($this->show) == 1) {
                $this->show = $this->show[0];
            }
        } else {
            if (!is_string($this->field) or !$this->model->hasAttribute($this->field)) {
                throw new CException("Related title fields not found in model definition.");
            }
        }

        /*
         * Get rows actually related to model
         */
        $this->relatedRows = $this->model->getRelated($this->relation->name);
        $this->relatedRows = (is_array($this->relatedRows)) ? $this->relatedRows : array($this->relatedRows);
        /*
         * Prepare select rows array with only PK values
         */
        if (null === $this->selectedRows) {
            $this->selectedRows = array();
            foreach ($this->relatedRows as $row) {
                $this->selectedRows[] = $row[$this->relatedField];
            }
        }
        /*
         * Get rows that can be related to model
         */
        $criteria = new CDbCriteria();
        if ($this->relation->className == get_class($this->model)) {
            /*
             * Exclude this to avoid recursion
             */
            if (!$this->model->getIsNewRecord() and !is_array($this->field) and !is_array($this->relatedField)) {
                $criteria->condition = $this->model->getMetaData()->tableSchema->primaryKey . ' <> :pk';
                $criteria->params = array(':pk' => $this->model->getPrimaryKey());
            }
        }
        $this->availableRows = $this->related->findAll($criteria);
    }

    /**
     * Called by CController::endWidget()
     */
    public function run()
    {
        switch (get_class($this->relation)) {

            case 'CManyManyRelation':
            case 'CHasManyRelation':
                $this->htmlOptions['multiple'] = 'multiple';
                #isset($this->htmlOptions['empty']) or $this->htmlOptions['empty'] = '';
                $name = get_class($this->model) . '[' . $this->relation->name . ']';
                $items = Helper::listData($this->availableRows, $this->relatedField, $this->show, null, $this->separator);
                echo CHtml::hiddenField(get_class($this->model) . '[__relations][]', $this->relation->name);
                echo CHtml::listBox($name, $this->selectedRows, $items, $this->htmlOptions);
                break;

            case 'CBelongsToRelation':
            case 'CHasOneRelation':
                isset($this->htmlOptions['empty']) or $this->htmlOptions['empty'] = '';
                $items = Helper::listData($this->availableRows, $this->relatedField, $this->show, null, $this->separator);
                echo $this->form->dropDownList($this->model, $this->field, $items, $this->htmlOptions);
                break;

            case 'CStatRelation':
                echo 'CStatRelation not implemented.';
                break;

            default:
                echo get_class($this->relation) . ' relation type unrecognized.';
        }
    }

}