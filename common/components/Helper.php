<?php

/**
 * Common useful helper functions.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class Helper
{

    /**
     * Evolution of CHtml::listData
     *
     * + Adds ability to concatenate textfields with separator.
     */
    public static function listData(array $models, $valueField, $textField, $groupField = null, $separator = ' - ')
    {
        $listData = array();
        if (null === $groupField) {
            foreach ($models as $model) {
                if (is_array($textField)) {
                    $textFields = $textField;
                    $first = array_shift($textFields);
                    $text = CHtml::value($model, $first);
                    foreach ($textFields as $key) {
                        $text .= $separator . CHtml::value($model, $key);
                    }
                } else {
                    $text = CHtml::value($model, $textField);
                }
                $value = CHtml::value($model, $valueField);
                $listData[$value] = $text;
            }
        } else {
            foreach ($models as $model) {
                if (is_array($textField)) {
                    $textFields = $textField;
                    $first = array_shift($textFields);
                    $text = CHtml::value($model, $first);
                    foreach ($textFields as $key) {
                        $text .= $separator . CHtml::value($model, $key);
                    }
                } else {
                    $text = CHtml::value($model, $textField);
                }
                $group = CHtml::value($model, $groupField);
                $value = CHtml::value($model, $valueField);
                $listData[$group][$value] = $text;
            }
        }
        return $listData;
    }

}

