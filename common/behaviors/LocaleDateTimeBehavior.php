<?php

/**
 * LocaleDateTimeBehavior class.
 *
 * Format date and time fields according to locale.
 *
 * <code>
 *  'LocaleDateTimeBehavior' => array(
 *      'class' => 'common.behaviors.LocaleDateTimeBehavior',
 *      'dbDateFormat' =' => 'Y-m-d',
 *      'dbDateTimeFormat' =' => 'Y-m-d H:i:s',
 *      'defaultDateWidth' => 'medium',
 *      'defaultTimeWidth' => 'medium',
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
     * Format of saved date.
     *
     * @var string
     */
    public $dbDateFormat = 'Y-m-d';
    /**
     * Format of saved datetime.
     *
     * @var string
     */
    public $dbDateTimeFormat = 'Y-m-d H:i:s';
    /**
     * Format of date. [CDateFormatter]
     *
     * @var string
     */
    public $defaultDateWidth = 'medium';
    /**
     * Format of datetime. [CDateFormatter]
     *
     * @var string
     */
    public $defaultTimeWidth = 'medium';
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
        if ($type === 'time') {
            $dateWidth = null;
        } elseif (isset($this->attributes[$attribute]['dateWidth'])) {
            $dateWidth = $this->attributes[$attribute]['dateWidth'];
        } else {
            $dateWidth = $this->defaultDateWidth;
        }

        if ($type === 'date') {
            $timeWidth = null;
        } elseif (isset($this->attributes[$attribute]['timeWidth'])) {
            $timeWidth = $this->attributes[$attribute]['timeWidth'];
        } else {
            $timeWidth = $this->defaultTimeWidth;
        }

        if (!empty($timestamp)) {
            return Yii::app()->dateFormatter->formatDateTime($timestamp, $dateWidth, $timeWidth);
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
            if (isset($attribute['replaceNull'])
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

            if ($column->dbType === 'date') {
                $event->sender->$columnName = date(
                        $this->dbDateFormat, CDateTimeParser::parse($event->sender->$columnName, Yii::app()->locale->dateFormat)
                );
            } elseif ($column->dbType === 'datetime' || $column->dbType === 'timestamp') {
                $event->sender->$columnName = date(
                        $this->dbDateTimeFormat,
                        CDateTimeParser::parse(
                                $event->sender->$columnName,
                                strtr(
                                        Yii::app()->locale->dateTimeFormat,
                                        array(
                                        '{0}' => Yii::app()->locale->timeFormat,
                                        '{1}' => Yii::app()->locale->dateFormat
                                ))));
            }
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