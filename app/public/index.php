<?php

/**
 * Application entry script for Yii framework with customized project folders structure.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */

/*
 * Shortcut constants.
 */
define('DS', DIRECTORY_SEPARATOR);

/*
 * Prepares constants for paths and URLs.
 */
defined('PUBLIC_DIR') or define('PUBLIC_DIR', realpath(dirname(__FILE__)) . DS);
defined('ROOT_DIR') or define('ROOT_DIR', realpath(PUBLIC_DIR . '../..') . DS);
defined('COMMON_DIR') or define('COMMON_DIR', ROOT_DIR . 'common' . DS);
defined('VENDORS_DIR') or define('VENDORS_DIR', COMMON_DIR . 'vendors' . DS);
defined('YII_DIR') or define('YII_DIR', VENDORS_DIR . 'Yii' . DS);
defined('ZEND_DIR') or define('ZEND_DIR', VENDORS_DIR . 'Zend' . DS);
defined('PHPEXCEL_DIR') or define('PHPEXCEL_DIR', VENDORS_DIR . 'PHPExcel' . DS);
defined('PHPMAILER_DIR') or define('PHPMAILER_DIR', VENDORS_DIR . 'PHPMailer' . DS);
defined('APP_DIR') or define('APP_DIR', realpath(PUBLIC_DIR . '..') . DS);

set_include_path(VENDORS_DIR . PATH_SEPARATOR . ROOT_DIR . PATH_SEPARATOR . get_include_path());

/*
 * Get current enviroment.
 */
$enviroments = array(
        'localhost' => 'dev',
        'staging.server.ext' => 'staging',
        'production.server.ext' => 'production',
);
if (!isset($enviroments[$_SERVER['HTTP_HOST']])) {
    die('Environment configuration mapping for "' . $_SERVER['HTTP_HOST'] . '" not found !');
}
defined('APP_ENVIROMENT') or define('APP_ENVIROMENT', $enviroments[$_SERVER['HTTP_HOST']]);

/*
 * Turn on debug if not in production.
 */
define('YII_DEBUG', (APP_ENVIROMENT != 'production'));
if (YII_DEBUG) {
    /*
     * Specify how many levels of call stack should be shown in each log message.
     */
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
}

/*
 * Load Yii framework main class.
 */
if (!class_exists('Yii', false)) {
    require_once COMMON_DIR . 'vendors/Yii/yii.php';
}

/*
 * Integrate Zend Loader with Yii's own.
 */
require_once VENDORS_DIR . 'Zend/Loader/Autoloader.php';
spl_autoload_unregister(array('YiiBase', 'autoload'));
spl_autoload_register(array('Zend_Loader_Autoloader', 'autoload'));
spl_autoload_register(array('YiiBase', 'autoload'));

/*
 * Define application suite UNIQUE ID or get automatically from folder name.
 */
// define('APPSUITE_ID', 'your_own_unique_app_name');
// /*
if (!defined('APPSUITE_ID')) {
    $_parts = explode(DS, APP_DIR);
    define('APPSUITE_ID', $_parts[count($_parts) - 3]);
}
// */

/*
 * Define application UNIQUE ID or get automatically from folder name.
 */
// define('APP_ID', 'your_own_unique_app_name');
// /*
if (!defined('APP_ID')) {
    $_parts = explode(DS, APP_DIR);
    define('APP_ID', $_parts[count($_parts) - 2]);
}
// */

/*
 * Load configuration files merging app-specific with common ones for current environment.
 */
$commonConfig = require(ROOT_DIR . 'config/' . APP_ENVIROMENT . '.php');
$appConfig = require(APP_DIR . 'config/' . APP_ENVIROMENT . '.php');
$config = CMap::mergeArray($commonConfig, $appConfig);

/*
 * Redefine YII aliases.
 */
Yii::setPathOfAlias('RootAlias', ROOT_DIR);
Yii::setPathOfAlias('common', COMMON_DIR);
Yii::setPathOfAlias('system', YII_DIR);
Yii::setPathOfAlias('zii', YII_DIR . DS . 'zii');
Yii::setPathOfAlias('application', APP_DIR);
Yii::setPathOfAlias('webroot', PUBLIC_DIR);
Yii::setPathOfAlias('app', APP_DIR);
Yii::setPathOfAlias('ext', COMMON_DIR . DS . 'extensions');

/*
 * Some helpers.
 */
include VENDORS_DIR . 'php-helpers/array-helpers.php';
include VENDORS_DIR . 'php-helpers/string-helpers.php';

/*
 * Run !!!
 */
Yii::createWebApplication($config)->run();