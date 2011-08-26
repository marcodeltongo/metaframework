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
	 * Initializes the widget.
	 * This renders the form open tag.
	 */
	public function init()
	{
        parent::init();
	}

	/**
	 * Runs the widget.
	 * This registers the necessary javascript code and renders the form close tag.
	 */
	public function run()
	{
        parent::run();

        $this->widget('common.widgets.tooltip.jqTooltip');
        $this->widget('common.widgets.selectmenu.jqSelectMenu');
        $this->widget('common.widgets.multiselect.jqMultiSelect');
    }

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
    public function imageField($model, $attribute, $alias = 'thumb', $canDelete = true, $inputOptions = array(), $imgOptions = array())
    {
        /*
         * Compatibility check
         */
        if (is_array($alias)) {
            $inputOptions = $alias;
            $alias = 'thumb';
        }
        /*
         * Implementation
         */
        if ($model->isNewRecord or empty($model->$attribute)) {
            return $this->fileField($model, $attribute, $inputOptions);
        } else {
            $alt = (isset($imgOptions['alt'])) ? $imgOptions['alt'] : '';
            $html = Yii::app()->imageManager->html_alias($model->$attribute, $alias, $alt, $imgOptions);
            $inputOptions = CMap::mergeArray(array('style' => 'display: none'), $inputOptions);
            $modelName = get_class($model);

            if ($canDelete) {
                $js = 'var je = jQuery(this); je.fadeOut(\'fast\', function() { je.prev().fadeOut(\'fast\', function() { je.next().attr(\'value\',\'1\').next().next().next().fadeIn().next().remove(); }); });';
                $html .= '<input name="__deleteImage" type="button" onclick="' . $js . '" value="' . Yii::t('yii', 'Delete image') . '" />';
                $html .= '<input type="hidden" id="' . $modelName . '[' . $attribute . '__deleteImage]" name="' . $modelName . '[' . $attribute . '__deleteImage]" value="0" />';
                $html .= '<input type="hidden" id="' . $modelName . '[' . $attribute . '__oldImage]" name="' . $modelName . '[' . $attribute . '__oldImage]" value="' . $model->$attribute . '" />';
                $html .= $this->fileField($model, $attribute, $inputOptions);
                $html .= $this->hiddenField($model, $attribute);
            }

            return $html;
        }
    }

    /**
     * Renders a boolean input field for a model attribute.
     *
     * @param CModel $model the data model
     * @param string $attribute the attribute
     * @param array  $htmlOptions additional HTML attributes
     *
     * @return string the generated input field
     */
	public function booleanField($model, $attribute, $htmlOptions = array())
	{
		$htmlOptions['class'] = (isset($htmlOptions['class'])) ? $htmlOptions['class'] . ' boolean' : 'boolean';
		$htmlOptions['empty'] = (isset($htmlOptions['empty'])) ? $htmlOptions['empty'] : '';

		return $this->dropDownList($model, $attribute, array(Yii::t('yii', 'No'), Yii::t('yii', 'Yes')), $htmlOptions);
	}

}
