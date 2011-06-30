<?php

/**
 * UniqueMultiColumnValidator class.
 *
 * dtUniqueMultiColumnValidator validates that the union of two columns is unique.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class UniqueMultiColumnValidator extends CValidator
{
    /**
     * @property boolean
     */
    private $allowEmpty = false;
    /**
     * @property boolean
     */
    private $caseSensitive = false;

    /**
     * Property setter
     *
     * @param boolean
     */
    public function setAllowEmpty($value)
    {
        $this->allowEmpty = $value;
    }

    /**
     * Property getter
     *
     * @return boolean
     */
    public function getAllowEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * Property setter
     *
     * @param boolean
     */
    public function setCaseSensitive($value)
    {
        $this->caseSensitive = $value;
    }

    /**
     * Property getter
     *
     * @return boolean
     */
    public function getCaseSensitive()
    {
        return $this->caseSensitive;
    }

    /**
     * Validator
     *
     * @param object $object
     * @param string $attribute
     */
    protected function validateAttribute($object, $attribute)
    {
        $attributes = null;
        $criteria = array('condition' => '');
        if (false !== strpos($attribute, '+')) {
            $attributes = explode('+', $attribute);
        } else {
            $attributes = array($attribute);
        }

        foreach ($attributes as $attribute) {
            $value = $object->$attribute;
            if ($this->allowEmpty and ($value === null || $value === '')) {
                return;
            }

            $column = $object->getTableSchema()->getColumn($attribute);
            if ($column === null) {
                throw new CException(Yii::t('yii', '{class} does not have attribute "{attribute}".', array('{class}' => get_class($object), '{attribute}' => $attribute)));
            }
            $columnName = $column->rawName;

            if ('' != $criteria['condition']) {
                $criteria['condition'] .= " AND ";
            }

            $criteria['condition'] .= $this->caseSensitive ? "$columnName=:$attribute" : "LOWER($columnName)=LOWER(:$attribute)";
            $criteria['params'][':' . $attribute] = $value;
        }

        if ($column->isPrimaryKey) {
            $exists = $object->exists($criteria);
        } else {
            // need to exclude the current record based on PK
            $criteria['limit'] = 2;
            $objects = $object->findAll($criteria);
            $n = count($objects);
            if ($n === 1) {
                if ('' == $object->getPrimaryKey()) {
                    $exists = true;
                } else {
                    $exists = $objects[0]->getPrimaryKey() !== $object->getPrimaryKey();
                }
            }
            else
                $exists=$n > 1;
        }
        if ($exists) {
            $message = '';
            $labels = $object->attributeLabels();
            foreach ($attributes as $attribute) {
                $message .= $labels[$attribute] . '+';
            }
            $message = substr($message, 0, -1);
            $message = $this->message !== null ? $this->message : Yii::t('yii', 'The combination "{message}" must be unique.', array('{message}' => $message));
            foreach ($attributes as $attribute) {
                $this->addError($object, $attribute, $message);
            }
        }
    }

}

?>
