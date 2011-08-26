<?php

/**
 * LocaleDateTimeBehavior class.
 *
 * Format date and time fields according to locale.
 *
 * <code>
 *  'LocaleDateTimeBehavior' => array(
 *      'class' => 'common.behaviors.LocaleDateTimeBehavior',
 *      'autoAttributes' => false,
 *      'attributes' => array(
 *          'date',
 *          'start_time' => array(
 *              'dateWidth' => 'medium',
 *              'timeWidth' => 'medium',
 *          ),
 *          end_time' => array(
 *              'replaceNull' => null,
 *          ),
 *      ),
 *  ),
 * </code>
 *
 * @see CDateFormatter
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class LocaleDateTimeBehavior extends CActiveRecordBehavior
{
    /**
     * JQueryUI compatibles formats
     *
     * @var array
     */
    public $localeFormats = array(
            'en' => array(
                    'date' => 'MM/dd/yyyy',
                    'time' => 'HH:mm',
                    'datetime' => 'MM/dd/yyyy HH:mm',
            ),
            'es' => array(
                    'date' => 'dd/MM/yyyy',
                    'time' => 'HH:mm',
                    'datetime' => 'dd/MM/yyyy HH:mm',
            ),
            'it' => array(
                    'date' => 'dd/MM/yyyy',
                    'time' => 'HH:mm',
                    'datetime' => 'dd/MM/yyyy HH:mm',
            ),
    );
    /**
     * DB compatibles formats
     *
     * @var array
     */
    public $dbFormats = array(
            'date' => 'Y-m-d',
            'time' => 'H:i:s',
            'datetime' => 'Y-m-d H:i:s',
    );
    /**
     * Attributes to parse.
     *
     * @var array
     */
    public $attributes = array();
    /**
     * Autoparse all date/time model attributes.
     *
     * @var string
     */
    public $autoAttributes = false;
    /**
     * Autoreplace empty date/time with null
     *
     * @var boolean
     */
    public $autoReplaceNull = true;

    /**
     * Formats an attribute for views
     *
     * @param string Name of the attribute
     * @param string Timestamp
     * @param string Type of the attribute
     *
     * @return string Formatted (localized) date or time
     */
    protected function formatDateTimeForViews($attribute, $timestamp, $type)
    {
        $type = ($type == 'timestamp') ? 'datetime' : $type;
        if (!empty($timestamp)) {
            return Yii::app()->dateFormatter->format($this->localeFormats[Yii::app()->getLanguage()][$type], $timestamp);
        } elseif (isset($this->attributes[$attribute]['replaceNull'])) {
            return $this->attributes[$attribute]['replaceNull'];
        }

        return null;
    }

    /**
     * Replace null-placeholders.
     */
    public function beforeValidate($event)
    {
        foreach ($this->attributes as $attributeName => $attribute) {
            if ($this->autoReplaceNull and $this->autoAttributes
                    and isset($event->sender->$attributeName)
                    and (intval($event->sender->$attributeName) == 0)) {
                $event->sender->$attributeName = null;
            } elseif (isset($attribute['replaceNull'])
                    and isset($event->sender->$attributeName)
                    and ($event->sender->$attributeName == $attribute['replaceNull'])) {
                $event->sender->$attributeName = null;
            }
        }
    }

    /**
     * Formats for DB.
     */
    public function beforeSave($event)
    {
        foreach ($event->sender->tableSchema->columns as $columnName => $column) {
            if (!$this->autoAttributes
                    and !in_array($columnName, $this->attributes)
                    and !array_key_exists($columnName, $this->attributes)) {
                continue;
            }

            if (!in_array($column->dbType, array('date', 'time', 'datetime', 'timestamp'))) {
                continue;
            }

            if (empty($event->sender->$columnName)) {
                $event->sender->$columnName = null;
                continue;
            }

            $type = ($column->dbType == 'timestamp') ? 'datetime' : $column->dbType;
            $format = $this->localeFormats[Yii::app()->getLanguage()][$type];
            $value = $event->sender->$columnName;
            if ($type == 'datetime' and strlen($value)+3 == strlen($format)) {
                $value .= ':00';
            }
            $timestamp = CDateTimeParser::parse($value, $format);
            $event->sender->$columnName = date($this->dbFormats[$type], $timestamp);
        }
    }

    /**
     * Formats for App.
     */
    public function afterFind($event)
    {
        foreach ($event->sender->tableSchema->columns as $columnName => $column) {
            if (!$this->autoAttributes
                    and !in_array($columnName, $this->attributes)
                    and !array_key_exists($columnName, $this->attributes)) {
                continue;
            }

            if (!in_array($column->dbType, array('date', 'time', 'datetime', 'timestamp'))) {
                continue;
            }

            $event->sender->$columnName = $this->formatDateTimeForViews($columnName, $event->sender->$columnName, $column->dbType);
        }
    }

}
