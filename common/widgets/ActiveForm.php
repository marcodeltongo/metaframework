<?php

/**
 * ActiveForm is the customized base active form class.
 *
 * All active forms for this application should extend from this base class.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class ActiveForm extends CActiveForm
{

    /**
     * Renders a file input field for a model attribute or image if present.
     *
     * @param CModel $model the data model
     * @param string $attribute the attribute
     * @param string $alias
     * @param bool   $canDelete
     * @param array  $inputOptions additional HTML attributes
     * @param array  $imgOptions additional HTML attributes
     *
     * @return string the generated input field
     */
    public function imageField($model, $attribute, $alias = 'edit-view', $canDelete = false, $inputOptions = array(), $imgOptions = array())
    {
        if ($model->isNewRecord or empty($model->$field)) {
            return $form->fileField($model, $field, $inputOptions);
        } else {
            $alt = (isset($imgOptions['alt'])) ? $imgOptions['alt'] : '';
            $html = Yii::app()->imageManager->html_alias($model->$field, $alias, $alt, $imgOptions);

            if ($canDelete) {
                $js = 'var je = jQuery(this); je.prev().fadeOut(); je.fadeOut(); jQuery(\'#' . $field . '_delete\').attr(\'value\',\'1\'); je.next().next().next().fadeIn();';
                $html .= '<input type="button" onclick="' . $js . '" value="' . Yii::t('yii', 'Delete image') . '" />';
                $html .= '<input type="hidden" id="' . $field . '_delete" name="' . $field . '_delete" value="0" />';
                $html .= $form->fileField($model, $field, array('style' => 'display: none'));
            }

            return $html;
        }
    }

}