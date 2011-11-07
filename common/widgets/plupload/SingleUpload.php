<?php

/**
 * SingleUpload widget class file.
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
class SingleUpload extends CWidget
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
    private $_cssFiles = array();
    /**
     * JS files to include
     *
     * @var object
     */
    private $_jsFiles = array('plupload.full.js');

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
			'browse_button' => $this->selector,
		), $this->options));

		$js = "var Uploader = new plupload.Uploader({$encodedOptions});
		Uploader.init();
		Uploader.bind('FilesAdded', function(up, files) {
			jQuery('#{$this->options['browse_button']}').fadeOut();
			up.start();
		});
		Uploader.bind('UploadComplete', function(up, files) {
			jQuery('{$this->selector}').val(files[0].name);
		});
		Uploader.bind('Error', function(up, args) {
			console.log(args.code + ': ' + args.message);
		});";

        $this->_clientScript->registerScript('Yii.' . get_class($this), $js, CClientScript::POS_END);
    }

}