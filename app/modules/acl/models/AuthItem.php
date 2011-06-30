<?php

/**
 * This is the model class for table "AuthItem".
 *
 * The followings are the available columns:
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $bizrule
 * @property string $data
 *
 * The followings are the available model relations:
 * @property AuthAssignment[] $assignments
 * @property AuthItemChild[] $parents
 * @property AuthItemChild[] $children
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class AuthItem extends ActiveRecord
{
    /**
     * @var array
     */
    public static $TYPES = array('Operation', 'Task', 'Role');
    /**
     *
     * @var string
     */
    public $oldName;

    /**
     * Returns the static model class.
     *
     * @return AuthItem the static model class.
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Retrieves the associated database table name.
     *
     * @return string the associated database table name.
     */
    public function tableName()
    {
        return Yii::app()->authManager->itemTable;
    }

    /**
     * Retrieves the list of validation rules for model attributes.
     *
     * @see http://www.yiiframework.com/doc/api/1.1/CModel#rules-detail
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('name, type', 'required'),
                array('type', 'numerical', 'integerOnly' => true),
                array('type', 'in', 'range' => array(0, 1, 2)),
                array('name', 'length', 'max' => 255),
                array('name', 'filter', 'filter' => 'strtolower'),
                array('description, bizrule, data', 'safe'),
                array('name, type, description', 'safe', 'on' => 'search'),
        );
    }

    /**
     * Retrieves the list of relational rules.
     *
     * @see http://www.yiiframework.com/doc/api/1.1/CActiveRecord#relations-detail
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'assignments' => array(self::HAS_MANY, 'AuthAssignment', 'itemname', 'condition' => '', 'order' => '', /* ... */),
                'parents' => array(self::MANY_MANY, 'AuthItem', 'AuthItemChild(child, parent)', 'condition' => '', 'order' => '', /* ... */),
                'children' => array(self::MANY_MANY, 'AuthItem', 'AuthItemChild(parent, child)', 'condition' => '', 'order' => '', /* ... */),
        );
    }

    /**
     * Retrieves the list of customized attribute labels (name => label)
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'name' => Yii::t('AclModule_labels', 'Name'),
                'type' => Yii::t('AclModule_labels', 'Type'),
                'description' => Yii::t('AclModule_labels', 'Description'),
                'bizrule' => Yii::t('AclModule_labels', 'Bizrule'),
                'data' => Yii::t('AclModule_labels', 'Data'),
                # Relations
                'assignments' => Yii::t('AclModule_labels', 'Assigned to'),
                'parents' => Yii::t('AclModule_labels', 'Parents'),
                'children' => Yii::t('AclModule_labels', 'Children'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('name', $this->name, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('description', $this->description, true);

        return new CActiveDataProvider('AuthItem', array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Returns the list of attributes for jqGrid.
     *
     * @return array
     */
    public function gridAttributes()
    {
        return array(
            'name',
            'type',
            'description'
        );
    }

    // ------------------------------------------------------------------------

    protected function beforeSave()
    {
        $this->data = serialize($this->data);
        return parent::beforeSave();
    }

    protected function afterFind()
    {
        parent::afterFind();
        $this->data = unserialize($this->data);
    }

    protected function afterSave()
    {
        parent::afterSave();
        $this->data = unserialize($this->data);
        /*
         * Cascade name update
         */
        if (!empty($this->oldName) and ($this->oldName != $this->name)) {
            $this->model()->updateByPk($this->oldName, array("name" => $this->name));
            $criteria = new CDbCriteria();
            $criteria->condition = "itemname='" . $this->oldName . "'";
            AuthAssignment::model()->updateAll(array('itemname' => $this->name), $criteria);
            $criteria->condition = "parent='" . $this->oldName . "'";
            AuthItemChild::model()->updateAll(array('parent' => $this->name), $criteria);
            $criteria->condition = "child='" . $this->oldName . "'";
            AuthItemChild::model()->updateAll(array('child' => $this->name), $criteria);
        }
    }

    protected function beforeDelete()
    {
        if (parent::beforeDelete()) {
            /*
             * Cascade delete
             */
            AuthAssignment::model()->deleteAll("itemname='" . $this->name . "'");
            AuthItemChild::model()->deleteAll("parent='" . $this->name . "'");
            AuthItemChild::model()->deleteAll("child='" . $this->name . "'");
            return true;
        }

        return false;
    }

}