<?php

/**
 * ImageBehavior class.
 *
 * ImageBehavior allows a model "image" attributes to be uploaded.
 *
 * <code>
 * public function behaviors()
 * 	{
 * 		return array(
 * 			'ImageBehavior' => array(
 * 				'class' => 'common.behaviors.ImageBehavior',
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
class ImageBehavior extends CActiveRecordBehavior
{
    /**
     * The model "image" attributes to be uploaded.
     *
     * @var array
     */
    public $attributes = array();

    /**
     * Responds to {@link CModel::onBeforeSave} event.
     *
     * Validate uploaded files and saves to managed folder.
     *
     * @param CModelEvent $event
     */
    public function beforeSave($event)
    {
        $owner = $this->getOwner();
        $class = get_class($owner);

        if (!isset($_FILES[$class])) {
            return;
        }

        /*
         * Manage uploads
         */
        foreach ($this->attributes as $name) {
            /*
             * Remove image ?
             */
            if (isset($_POST[$class][$name . '__deleteImage'])
                    and (1 == $_POST[$class][$name . '__deleteImage'])) {
                Yii::app()->imageManager->deleteAll($owner->$name);
                $owner->$name = '';
            }
            /*
             * Upload image ?
             */
            if (isset($_FILES[$class]['name']) and !empty($_FILES[$class]['name'][$name])) {
                $image = Yii::app()->imageManager->upload($_FILES[$class], $name);
                if (false === $image) {
                    $event->isValid = false;
                    $owner->addError($name, Yii::app()->imageManager->error);
                } else {
                    $owner->$name = $image;
                }
            }
        }
    }

}
