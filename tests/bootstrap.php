<?php

/**
 * Tests entry script for Yii framework with customized project folders structure.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */

/*
 * APP ID
 */
define('APP_ID', 'meta_framework');

/*
 * Prepares constants for paths and URLs.
 */
defined('PUBLIC_DIR') or define('PUBLIC_DIR', realpath(dirname(__FILE__) . '/../app/public') . '/');
defined('ROOT_DIR') or define('ROOT_DIR', realpath(dirname(__FILE__) . '/../') . '/');
defined('COMMON_DIR') or define('COMMON_DIR', ROOT_DIR . 'common/');
defined('VENDORS_DIR') or define('VENDORS_DIR', COMMON_DIR . 'vendors/');
defined('YII_DIR') or define('YII_DIR', VENDORS_DIR . 'Yii/');
defined('ZEND_DIR') or define('ZEND_DIR', VENDORS_DIR . 'Zend/');
defined('PHPEXCEL_DIR') or define('PHPEXCEL_DIR', VENDORS_DIR . 'PHPExcel/');
defined('PHPMAILER_DIR') or define('PHPMAILER_DIR', VENDORS_DIR . 'PHPMailer/');
defined('APP_DIR') or define('APP_DIR', realpath(PUBLIC_DIR . '..') . '/');
defined('TEST_DIR') or define('TEST_DIR', ROOT_DIR . 'tests/');

set_include_path(VENDORS_DIR . PATH_SEPARATOR . ROOT_DIR . PATH_SEPARATOR . get_include_path());

define('APP_ENVIROMENT', 'dev');
define('YII_DEBUG', true);
define('YII_TRACE_LEVEL', 3);

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

Yii::setPathOfAlias('RootAlias', ROOT_DIR);
Yii::setPathOfAlias('common', COMMON_DIR);
Yii::setPathOfAlias('system', YII_DIR);
Yii::setPathOfAlias('zii', YII_DIR . DIRECTORY_SEPARATOR . 'zii');
Yii::setPathOfAlias('application', APP_DIR);
Yii::setPathOfAlias('webroot', PUBLIC_DIR);
Yii::setPathOfAlias('app', APP_DIR);
Yii::setPathOfAlias('ext', COMMON_DIR . DIRECTORY_SEPARATOR . 'extensions');

/*
 * Some helpers.
 */
include VENDORS_DIR . 'Logicoder/array-helpers.php';
include VENDORS_DIR . 'Logicoder/string-helpers.php';

/*
 * Load configuration.
 */
$commonConfig = require(COMMON_DIR . 'config/dev.php');
$appConfig = require(APP_DIR . 'config/dev.php');
$devConfig = CMap::mergeArray($commonConfig, $appConfig);
$config = CMap::mergeArray($devConfig, array(
    'components' => array(
        'fixture' => array(
            'class' => 'system.test.CDbFixtureManager',
            'basePath' => TEST_DIR . 'fixtures/',
        ),
    ),
));

/*
 * Load test classes.
 */
require_once(YII_DIR . 'yiit.php');
require_once(TEST_DIR . 'WebTestCase.php');

/*
 * Run !
 */
Yii::createWebApplication($config);
