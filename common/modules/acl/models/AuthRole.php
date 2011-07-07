<?php

/**
 * Extends AuthItem, only roles.
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
class AuthRole extends AuthItem
{
    public $type = CAuthItem::TYPE_ROLE;

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
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('name', $this->name, true);
        $criteria->compare('type', CAuthItem::TYPE_ROLE);
        $criteria->compare('description', $this->description, true);

        return new CActiveDataProvider('AuthItem', array(
                'criteria' => $criteria,
        ));
    }
}