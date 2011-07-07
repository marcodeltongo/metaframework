<?php

/**
 * This is the model class for table "AuthItemChild".
 *
 * The followings are the available columns:
 * @property string $parent
 * @property string $child
 *
 * The followings are the available model relations:
 * @property AuthItem $parentItem
 * @property AuthItem $childItem
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class AuthItemChild extends ActiveRecord
{

    /**
     * Returns the static model class.
     *
     * @return AuthItemChild the static model class.
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
        return Yii::app()->authManager->itemChildTable;
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
                array('parent, child', 'required'),
                array('parent, child', 'length', 'max' => 255),
                array('child', 'compare', 'operator' => '!=', 'compareAttribute' => 'parent'),
                array('parent, child', 'safe', 'on' => 'search'),
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
			'parentItem' => array(self::BELONGS_TO, 'AuthItem', 'parent', 'condition' => '', 'order' => ''),
			'childItem' => array(self::BELONGS_TO, 'AuthItem', 'child', 'condition' => '', 'order' => ''),
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
                'parent' => Yii::t('AclModule', 'Parent ID'),
                'child' => Yii::t('AclModule', 'Child ID'),
                # Relations
                'parentItem' => Yii::t('AclModule', 'Parent Item'),
                'childItem' => Yii::t('AclModule', 'Child Item'),
        );
    }

    /**
     * Retrieves the list of customized attribute titles
     *
     * @return array customized attribute labels
     */
    public function attributeTitles()
    {
        return array(
			'parent' => Yii::t('AclModuleChild', ''),
			'child' => Yii::t('AclModuleChild', ''),

			# Relations
			'parentItem' => Yii::t('AclModuleChild', ''),
			'childItem' => Yii::t('AclModuleChild', ''),
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

        $criteria->compare('parent', $this->parent, true);
        $criteria->compare('child', $this->child, true);

        return new CActiveDataProvider('AuthItemChild', array(
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
            // Remove attributes that should not be available in the grid.
			'parent',
			'child',
        );
    }
}