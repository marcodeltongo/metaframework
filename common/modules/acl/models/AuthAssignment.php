<?php

/**
 * This is the model class for table "AuthAssignment".
 *
 * The followings are the available columns:
 * @property integer $id
 * @property string $itemname
 * @property string $userid
 * @property string $bizrule
 * @property string $data
 *
 * The followings are the available model relations:
 * @property AuthItem $items
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class AuthAssignment extends ActiveRecord
{

    /**
     * Returns the static model class.
     *
     * @return AuthAssignment the static model class.
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
        return Yii::app()->authManager->assignmentTable;
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
            array('itemname, userid', 'required'),
            array('itemname, userid', 'length', 'max' => 255),
            array('bizrule, data', 'safe'),

            array('id, itemname, userid', 'safe', 'on' => 'search'),
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
			'item' => array(self::BELONGS_TO, 'AuthItem', 'itemname', 'condition' => '', 'order' => '', /* ... */),
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
            'id' => Yii::t('AclModule', 'ID'),
            'itemname' => Yii::t('AclModule', 'Item name'),
            'userid' => Yii::t('AclModule', 'UserID'),
            'bizrule' => Yii::t('AclModule', 'Bizrule'),
            'data' => Yii::t('AclModule', 'Data'),
			# Relations
			'item' => Yii::t('AclModule', 'Items'),
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
			'id' => Yii::t('AclModule', ''),
			'itemname' => Yii::t('AclModule', ''),
			'userid' => Yii::t('AclModule', ''),
			'bizrule' => Yii::t('AclModule', ''),
			'data' => Yii::t('AclModule', ''),

			# Relations
			'item' => Yii::t('AclModule', ''),
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

		$criteria->compare('id', $this->id);
		$criteria->compare('itemname', $this->itemname, true);
		$criteria->compare('userid', $this->userid, true);

        return new CActiveDataProvider('AuthAssignment', array(
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
            'id',
			'itemname',
			'userid',
			'bizrule',
//			'data',
        );
    }
}