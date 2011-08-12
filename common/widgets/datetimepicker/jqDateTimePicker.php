<?php

/**
 * jqDateTimePicker widget class file.
 *
 * Proxy for jQueryUI extended DateTimePicker from:
 * https://github.com/trentrichardson/jQuery-Timepicker-Addon
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class jqDateTimePicker extends CJuiDatePicker
{
	const ASSETS_NAME = '/jquery-ui-timepicker-addon';

	/**
	 * Working mode ['date', 'time', 'datetime']
	 *
	 * @var string
	 */
	public $mode = 'datetime';

	/**
	 * Check parameters and try auto-detection.
	 * Called by CController::beginWidget()
	 */
	public function init()
	{
		if (!in_array($this->mode, array('date', 'time', 'datetime'))) {
			throw new CException('unknow mode "' . $this->mode . '"');
		}
		if (!isset($this->language)) {
			$this->language = Yii::app()->getLanguage();
		}
		return parent::init();
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
		list($name, $id) = $this->resolveNameID();

		if (isset($this->htmlOptions['id'])) {
			$id = $this->htmlOptions['id'];
		} else {
			$this->htmlOptions['id'] = $id;
		}

		if (isset($this->htmlOptions['name'])) {
			$name = $this->htmlOptions['name'];
		} else {
			$this->htmlOptions['name'] = $name;
		}

		if ($this->hasModel()) {
			echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
		} else {
			echo CHtml::textField($name, $this->value, $this->htmlOptions);
		}

		$options = CJavaScript::encode($this->options);
		$cs = Yii::app()->getClientScript();

		$assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
		$cs->registerCssFile($assets . self::ASSETS_NAME . '.css');
		$cs->registerScriptFile($assets . self::ASSETS_NAME . '.js', CClientScript::POS_END);

		$js = "jQuery('#{$id}').{$this->mode}picker($options);";

		if (isset($this->language) and $this->language != 'en') {
			$this->registerScriptFile($this->i18nScriptFile);
			$cs->registerScriptFile($assets . '/localization/jquery-ui-timepicker-' . substr($this->language, 0, 2) . '.js', CClientScript::POS_END);
			$js = "jQuery('#{$id}').{$this->mode}picker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['{$this->language}'], {$options}));";
		}

		$cs->registerScript(__CLASS__, $this->defaultOptions ? 'jQuery.{$this->mode}picker.setDefaults(' . CJavaScript::encode($this->defaultOptions) . ');' : '');
		$cs->registerScript(__CLASS__ . '#' . $id, $js);
	}

}