<?php

/**
 * SlugValidator class.
 *
 * SlugValidator validates that the attribute value is a clean url and, if required, autocreates one from another field.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class SlugValidator extends CValidator
{
    /**
     * @property boolean whether the attribute value can be null or empty. Defaults to true,
     * meaning that if the attribute is empty, it is considered valid.
     */
    public $allowEmpty = false;
    /**
     * @property boolean whether the attribute value must be automatically overwritten.
     */
    public $overwrite = false;
    /**
     * @property string the attribute to take source string from.
     */
    public $getFrom = false;
    /**
     * @property boolean whether to try to append "_en" and "_it" to mappings.
     */
    public $languageSuffix = false;
    /**
     * @property boolean whether this validation rule should be skipped if when there are already errors. Defaults to true.
     */
    public $skipOnError = true;
    /**
     * @property string the user-defined error message. The placeholders "{attribute}" and "{value}"
     * are recognized, which will be replaced with the actual attribute name and value, respectively.
     */
    public $message;

    /**
     * Transform source string in a valid slug
     *
     * @param string $value
     * @return string
     */
    private function slugIt($value)
    {
        if (false === strpos($value, 'http')) {
            $value = trim($value);
            $value = iconv('ISO-8859-1', 'UTF-8', $value);
            $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
            $value = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $value);
            $value = strtolower(trim($value, '-'));
            $value = preg_replace("/[\/_|+ -]+/", '_', $value);
        }

        return $value;
    }

    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     */
    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;

        if (empty($value) and $this->allowEmpty)
            return;

        if ($this->languageSuffix) {
            $parts = explode('_', $attribute);
            $suffix = array_pop($parts);
            $getFrom = $this->getFrom . '_' . $suffix;
        } else {
            $getFrom = $this->getFrom;
        }

        if ((empty($value) or $this->overwrite) and $getFrom) {
            if (empty($object->$getFrom)) {
                $message = ($this->message !== null) ? $this->message : Yii::t('yii', '{attribute} cannot be blank.');
                $this->addError($object, $attribute, $message);
                return;
            } else {
                $object->$attribute = $this->slugIt($object->$getFrom);
                return;
            }
        }

        if (empty($value) and !$this->allowEmpty) {
            $message = ($this->message !== null) ? $this->message : Yii::t('yii', '{attribute} cannot be blank.');
            $this->addError($object, $attribute, $message);
            return;
        }

        $object->$attribute = $this->slugIt($value);
        return;
    }

}