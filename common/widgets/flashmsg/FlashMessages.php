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
 * @version 1.1
 */
class FlashMessages extends CWidget
{

	/**
	 * Selector for jQuery
	 *
	 * @var string
	 */
	public $selector = '[title]';
	/**
	 * Options
	 *
	 * @var array
	 */
	public $options = array();
	/**
	 * Base assets URL
	 *
	 * @var string
	 */
	private $_baseUrl = '';
	/**
	 * Client script object
	 *
	 * @var object
	 */
	private $_clientScript = null;
	/**
	 * CSS files to include
	 *
	 * @var object
	 */
	private $_cssFiles = array('flashmsg.css');
	/**
	 * JS files to include
	 *
	 * @var object
	 */
	private $_jsFiles = array('flashmsg.js');
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
	 * Check parameters and try auto-detection.
	 * Called by CController::beginWidget()
	 */
	public function init()
	{
		/**
		 * Publish the assets.
		 */
		$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
		$this->_baseUrl = Yii::app()->getAssetManager()->publish($dir);
	}

	/**
	 * Registers the external javascript files
	 */
	public function registerClientScripts()
	{
		$this->_clientScript = Yii::app()->getClientScript();

		foreach ($this->_cssFiles as $file) {
			$this->_clientScript->registerCssFile($this->_baseUrl . '/' . $file);
		}

		foreach ($this->_jsFiles as $file) {
			$this->_clientScript->registerScriptFile($this->_baseUrl . '/' . $file, CClientScript::POS_END);
		}
	}

	/**
	 * Render the widget
	 */
	public function run()
	{
		$this->registerClientScripts();
		$encodedOptions = CJavaScript::encode(array_merge(array(), $this->options));
		$js = "";
		$this->_clientScript->registerScript('Yii.' . get_class($this), $js, CClientScript::POS_END);

		$markup = '';
		foreach ($this->keys as $key) {
			foreach (Yii::app()->user->getFlash($key, array()) as $value) {
				$markup .= strtr($this->template, array(
					'{key}' => $key,
					'{message}' => $value,
				));
			}
		}
		if ($markup !== '') {
			echo CHtml::openTag('div', $this->htmlOptions), $markup, CHtml::closeTag('div');
		}
	}

}
