<?php

/**
 * jqGrid widget class file.
 *
 * Proxy for jqGrid - {see @link http://www.trirand.com/blog/}
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class jqGrid extends CWidget
{
    /**
     * jqGrid HTML ID.
     *
     * @var string
     */
    public $id = false;
    /**
     * jqGrid language.
     *
     * @var string
     */
    public $language = false;
    /**
     * jqGrid options.
     *
     * @var array
     */
    public $options = array();
    /**
     * Whether to use a navbar in the bottom of the grid.
     *
     * @var boolean
     */
    public $useNavBar = true;
    /**
     * jqGrid navbar options.
     *
     * @var array
     */
    public $navBarOptions = array('edit' => false, 'add' => false, 'del' => false);
    /**
     * jqGrid table options.
     *
     * @var array
     */
    public $tableOptions = array('class' => 'scroll', 'cellpadding' => 0, 'cellspacing' => 0);
    /**
     * jqGrid pager options.
     *
     * @var array
     */
    public $pagerOptions = array('class' => 'scroll', 'style' => 'text-align: center;');
    /**
     * Base assets URL.
     *
     * @var string
     */
    private $_baseUrl = '';
    /**
     * Client script object.
     *
     * @var object
     */
    private $_clientScript = null;
    /**
     * Valid plugin languages.
     *
     * @var string
     */
    protected $validLanguages = array('bg', 'cs', 'de', 'dk', 'el', 'en', 'fa', 'fi', 'fr', 'is', 'it', 'pl', 'pt-br', 'pt', 'ru', 'es', 'sv', 'tr');

    /**
     * Check parameters and try auto-detection.
     * Called by CController::beginWidget()
     */
    public function init()
    {
        /*
         * Language
         */
        if (false === $this->language) {
            $this->language = Yii::app()->getLanguage();
        }
        $lang = (($p = strpos($this->language, '_')) !== false) ? str_replace('_', '-', $this->language) : $this->language;
        if (in_array($lang, $this->validLanguages)) {
            $this->language = $lang;
        } else {
            $suffix = empty($lang) ? 'en' : ($p !== false) ? strtolower(substr($lang, 0, $p)) : strtolower($lang);
            if (in_array($suffix, $this->validLanguages)) {
                $this->language = $suffix;
            }
        }

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
        $files = array();
        $subdir = '';
        $subfile = '';

        $this->_clientScript = Yii::app()->getClientScript();
        $this->_clientScript->registerCssFile($this->_baseUrl . '/css/ui.jqgrid.css');
        $this->_clientScript->registerCssFile($this->_baseUrl . '/plugins/ui.multiselect.css');
        $this->_clientScript->registerScriptFile($this->_baseUrl . '/js/i18n/grid.locale-' . $this->language . '.js', CClientScript::POS_END);

        $files = array("jquery.jqGrid.min.js"); // jqGrid

        $plugins = array(
                //'grid.addons.js',
                'grid.postext.js',
                'grid.setcolumns.js',
                'jquery.contextmenu.js',
                'jquery.searchFilter.js',
                'jquery.tablednd.js',
                'ui.multiselect.js',
        );

        foreach ($files as $file) {
            $this->_clientScript->registerScriptFile($this->_baseUrl . '/js/' . $file, CClientScript::POS_END);
        }

        foreach ($plugins as $file) {
            $this->_clientScript->registerScriptFile($this->_baseUrl . '/plugins/' . $file, CClientScript::POS_END);
        }
    }

    /**
     * Make the options javascript string.
     *
     * @return string
     */
    protected function makeOptions($id)
    {
        $options = array();

        if ($this->useNavBar) {
            $options['pager'] = $id . '-pager';
        }

        $encodedOptions = CJavaScript::encode(array_merge($options, $this->options));

        return $encodedOptions;
    }

    /**
     * Generate the javascript code.
     *
     * @param string $id id
     * @return string
     */
    protected function jsCode($id)
    {
        $options = $this->makeOptions($id);
        $navOptions = CJavaScript::encode($this->navBarOptions);

        $nav = '';
        if ($this->useNavBar) {
            $nav = ".navGrid('#{$id}-pager', {$navOptions})";
        }

        $script = "$('#{$id}').jqGrid({$options}){$nav};";

        return $script;
    }

    /**
     * Make the HTML code
     *
     * @param string $id id
     * @return string
     */
    protected function htmlCode($id)
    {
        if (!isset($this->tableOptions['id'])) {
            $this->tableOptions['id'] = $id;
        }
        if (!isset($this->pagerOptions['id'])) {
            $this->pagerOptions['id'] = $id . '-pager';
        }

        $html = CHtml::tag('table', $this->tableOptions, '', true) . "\n";
        if ($this->useNavBar) {
            $html .= CHtml::tag('div', $this->pagerOptions, '', true);
        }

        return $html;
    }

    /**
     * Render the widget
     */
    public function run()
    {
        if (false === $this->id) {
            $this->id = $this->getId();
        }

        $this->registerClientScripts();

        $js = $this->jsCode($this->id);
        $this->_clientScript->registerScript('Yii.' . get_class($this) . '#' . $this->id, $js, CClientScript::POS_END);

        echo $this->htmlCode($this->id);
    }

}