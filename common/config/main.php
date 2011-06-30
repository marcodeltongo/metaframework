<?php

/**
 * Common main configuration.
 */
return array(
    'basePath' => APP_DIR,
    'name' => APP_ID,
    'language' => 'en',
    'sourceLanguage' => 'en',
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'common.vendors.*',
        'common.behaviors.*',
        'common.validators.*',
        'common.components.*',
        'common.extensions.*',
        'common.models.*',
        'common.widgets.*',
        'application.models.*',
        'application.behaviors.*',
        'application.components.*',
    ),
    'modules' => array(
    ),
    'components' => array(
        'session' => array(
            'sessionName' => APP_ID,
        ),
        'cache' => array(
            'class' => 'system.caching.CFileCache',
            'cachePath' => APP_DIR . 'runtime/cache',
        ),
        'request' => array(
            'enableCsrfValidation' => true,
            'enableCookieValidation' => true,
        ),
        'db' => array(
            'charset' => 'utf8',
            'emulatePrepare' => true,
            'schemaCachingDuration' => '3600',
            'enableProfiling' => false,
            'enableParamLogging' => false,
            'nullConversion' => PDO::NULL_EMPTY_STRING,
            'initSQLs' => array(
                "SET time_zone = '+01:00';",
                "SET NAMES utf8;",
            )
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                // defaults to '<controller:>/<action:>' => '<controller>/<action>',
            ),
        ),
        'authManager' => array(
            'class' => 'CDbAuthManager',
            'defaultRoles' => array('authenticated', 'guest'),
        ),
        'clientScript' => array(
            'scriptMap' => array(
                'jquery.js' => false,
                'jquery-ui.js' => false,
                'jquery.min.js' => false,
                'jquery-ui.min.js' => false,
                'jquery.metadata.js' => false,
            ),
            'coreScriptPosition' => CClientScript::POS_END,
        ),
        /*
            Custom components
        */
        'mailer' => array(
            'class' => 'Mailer',
        ),
        'browser' => array(
            'class' => 'Browser',
        ),
        'imageManager' => array(
            'class' => 'ImageManager',
        ),
        'file' => array(
            'class'=>'common.extensions.CFile.CFile',
        ),
    ),
);