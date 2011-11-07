<?php

/**
 * MultiUpload widget class file.
 *
 * Proxy for the Plupload plugin
 * @see http://www.plupload.com/
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class MultiUpload extends CWidget
{
	/**
	 * Selector for jQuery
	 *
	 * @var string
	 */
	public $selector = '.upload';
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
    private $_cssFiles = array('jquery.ui.plupload/css/jquery.ui.plupload.css');
    /**
     * JS files to include
     *
     * @var object
     */
    private $_jsFiles = array('plupload.full.js', 'jquery.ui.plupload/jquery.ui.plupload.js');

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

        $encodedOptions = CJavaScript::encode(array_merge(array(
			'runtimes' => (Yii::app()->browser->is_firefox()) ? 'html5' : 'flash',
			'urlstream_upload' => true,
			'flash_swf_url' => $this->_baseUrl . '/plupload.flash.swf',
			'max_file_size' => '20mb',
			'init' => array(
				'UploadComplete' => 'js:function(up, files) { var dst = jQuery("'.$this->options['destination'].'"); jQuery(files).each(function () { dst.val(dst.val() + "|" + this.name); }); }',
			),
		), $this->options));

		$js = "jQuery('{$this->selector}').plupload({$encodedOptions});";

        $this->_clientScript->registerScript('Yii.' . get_class($this), $js, CClientScript::POS_END);
    }

}