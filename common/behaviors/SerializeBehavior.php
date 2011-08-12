<?php

/**
 * SerializeBehavior class.
 *
 * SerializeBehavior allows a model to specify some attributes to be
 * arrays and serialized upon save and unserialized after a Find() function
 * is called on the model. Uses base64 to avoid problems with MySQL TEXT.
 *
 * <code>
 * public function behaviors()
 * 	{
 * 		return array(
 * 			'SerializeBehavior' => array(
 * 				'class' => 'common.behaviors.SerializeBehavior',
 * 				'attributes' => array('field_a', 'field_b', 'field_c'),
 * 			)
 * 		);
 * 	}
 * </code>
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class SerializeBehavior extends CActiveRecordBehavior
{
    /**
     * The name of the attribute(s) to serialize/unserialize
     *
     * @var array
     */
    public $attributes = array();

    /**
     * Convert the array value to serialized string.
     */
    private function _serialize ()
    {
        if (!empty($this->attributes)) {
            foreach ($this->attributes as $attribute) {
                $_att = $this->getOwner()->$attribute;

                /*
                 *  check if the attribute is an array, and serialize it
                 */
                if (is_array($_att)) {
                    $this->getOwner()->$attribute = base64_encode(serialize($_att));
                } else {
                    /*
                     * if its a string, lets see if its unserializable, if not NULL
                     */
                    if (is_scalar($_att)) {
                        $a = @unserialize(base64_decode($_att));
                        if ($a === false) {
                            $this->getOwner()->$attribute = null;
                        }
                    }
                }
            }
        }
    }

    /**
     * Convert the array value to serialized string.
     */
    private function _unserialize ()
    {
        if (!empty($this->attributes)) {
            foreach ($this->attributes as $attribute) {
                $_att = $this->getOwner()->$attribute;
                if (!empty($_att) and is_scalar($_att)) {
                    $a = @unserialize(base64_decode($_att));
                    if ($a !== false) {
                        $this->getOwner()->$attribute = $a;
                    } else {
                        $this->getOwner()->$attribute = array();
                    }
                }
            }
        }
    }

    /**
     * Responds to {@link CModel::onBeforeSave} event.
     * Convert the array value to serialized string.
     *
     * @param CModelEvent event
     */
    public function beforeSave($event)
    {
        $this->_serialize();
    }

    /**
     * Responds to {@link CModel::onAfterSave} event.
     * Convert the saved serialized string back into an array.
     *
     * @param CModelEvent event
     */
    public function afterSave($event)
    {
        $this->_unserialize();
    }

    /**
     * Responds to CActiveRecord::onBeforeFind event.
     * Convert the array value to serialized string.
     *
     * @param CModelEvent event
     */
    public function beforeFind($event)
    {
        $this->_serialize();
    }

    /**
     * Responds to {@link CModel::onAfterFind} event.
     * Convert the saved serialized string back into an array.
     *
     * @param CModelEvent event
     */
    public function afterFind($event)
    {
        $this->_unserialize();
    }

}
