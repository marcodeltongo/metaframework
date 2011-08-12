<?php

/**
 * jqChosen widget class file.
 *
 * Proxy for jQuery Chosen plugin from:
 * https://github.com/harvesthq/chosen
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class jqChosen extends CWidget
{
	/**
	 * Selector for jQuery
	 *
	 * @var string
	 */
	public $selector = '.chosen';
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
    private $_cssFiles = array('chosen.css');
    /**
     * JS files to include
     *
     * @var object
     */
    private $_jsFiles = array('chosen.jquery.min.js');

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

        $js = "jQuery('{$this->selector}').chosen({$encodedOptions});";

        $this->_clientScript->registerScript('Yii.' . get_class($this), $js, CClientScript::POS_END);
    }

}