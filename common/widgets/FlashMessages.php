<?php

/**
 * Flash messages widget class file.
 *
 * Displays flash messages to user.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class FlashMessages extends CWidget
{
    /**
     * @var array the keys for which to get flash messages.
     */
    public $keys = array('success', 'error', 'message');
    /**
     * @var string the template to use for displaying flash messages.
     */
    public $template = '<div class="flash-message flash-{key}">{message}</div>';
    /**
     * @var array the html options.
     */
    public $htmlOptions = array('id' => 'flash-messages-container');

    /**
     * Runs the widget.
     */
    public function run()
    {
        $markup = '';
        foreach ($this->keys as $key) {
            if (Yii::app()->user->hasFlash($key)) {
                $markup .= strtr($this->template, array(
                            '{key}' => $key,
                            '{message}' => Yii::app()->user->getFlash($key),
                        ));
            }
        }

        if ($markup !== '') {
            echo CHtml::openTag('div', $this->htmlOptions), $markup, CHtml::closeTag('div');
        }
    }

}
