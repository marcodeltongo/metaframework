<?php

/**
 * jqCRUDialog widget class file.
 *
 * Allows to create/update/delete model entry from JUI Dialog.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class jqCRUDialog extends CWidget
{

	/**
	 * Dialog ID
	 *
	 * @var string
	 */
	public $id = 'crudialog';
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
	private $_cssFiles = array();
	/**
	 * JS files to include
	 *
	 * @var object
	 */
	private $_jsFiles = array('crudialog.js');

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
		$defaults = array(
			'modal' => true,
			'autoOpen' => false,
			'resizable' => true,
			'title' => 'Dialog',
			'width' => 900,
			'height' => 'auto',
		);
		$this->options = array_merge($defaults, $this->options);
		$this->beginWidget('zii.widgets.jui.CJuiDialog', array('id' => $this->id, 'options' => $this->options));
		echo '<div class="crudialog-content"></div>';
		$this->endWidget();

		$this->registerClientScripts();
	}

}