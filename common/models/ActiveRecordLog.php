<?php

/**
 * This is the model class for table "ActiveRecordLog".
 *
 * The followings are the available columns:
 * @property integer $action
 * @property string $table
 * @property string $model
 * @property string $model_pk
 * @property string $before
 * @property string $after
 * @property string $user_pk
 * @property string $browser
 * @property string $host_info
 * @property string $timestamp
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class ActiveRecordLog extends CActiveRecord
{
    const ACTION_INSERT = 0;
    const ACTION_UPDATE = 1;
    const ACTION_DELETE = 2;

    /**
     * Returns the static model class.
     *
     * @return ActiveRecordLog the static model class.
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
        return 'ar_log';
    }

    /**
     * Retrieves the list of associated behaviors.
     *
     * @return array associated behaviors.
     */
    public function behaviors()
    {
        return array(
                'SerializeBehavior' => array(
                        'class' => 'common.behaviors.SerializeBehavior',
                        'serialAttributes' => array('before', 'after'),
                ),
                'CTimestampBehavior' => array(
                        'class' => 'zii.behaviors.CTimestampBehavior',
                        'createAttribute' => 'timestamp',
                        'updateAttribute' => null,
                )
        );
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
                array('action', 'numerical', 'integerOnly' => true),
                array('action', 'in', 'range' => array(0, 1, 2)),
                array('table, model, model_pk, user_pk, browser, host_info', 'length', 'max' => 255),
                array('before, after', 'safe'),
                array('action, table, model, model_pk, before, after, user_pk, browser, host_info, timestamp', 'safe', 'on' => 'search'),
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
                'action' => Yii::t('activerecordlog_labels', 'Action'),
                'table' => Yii::t('activerecordlog_labels', 'Table'),
                'model' => Yii::t('activerecordlog_labels', 'Model'),
                'model_pk' => Yii::t('activerecordlog_labels', 'Model Pk'),
                'before' => Yii::t('activerecordlog_labels', 'Before'),
                'after' => Yii::t('activerecordlog_labels', 'After'),
                'user_pk' => Yii::t('activerecordlog_labels', 'User Pk'),
                'browser' => Yii::t('activerecordlog_labels', 'User browser'),
                'host_info' => Yii::t('activerecordlog_labels', 'User host'),
                'timestamp' => Yii::t('activerecordlog_labels', 'Timestamp'),
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

        // Remove attributes that should not be searched.
        $criteria->compare('action', $this->action);
        $criteria->compare('table', $this->table, true);
        $criteria->compare('model', $this->model, true);
        $criteria->compare('model_pk', $this->model_pk, true);
        $criteria->compare('before', $this->before, true);
        $criteria->compare('after', $this->after, true);
        $criteria->compare('user_pk', $this->user_pk, true);
        $criteria->compare('browser', $this->browser, true);
        $criteria->compare('host_info', $this->host_info, true);
        $criteria->compare('timestamp', $this->timestamp, true);

        return new CActiveDataProvider('ActiveRecordLog', array(
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
			'action',
			'table',
			'model',
			'model_pk',
			'user_pk',
			'browser',
			'host_info',
			'timestamp',
        );
    }
}