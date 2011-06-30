<?php

/*
 * Inherits from common main.
 */
$commonMainConfig = require COMMON_DIR . 'config/main.php';

/**
 * Common development configuration.
 */
return CMap::mergeArray(array(
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=' . APP_ID,
            'username' => 'root',
            'password' => '',
            'enableProfiling' => true,
            'enableParamLogging' => true,
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'XWebDebugRouter',
                    'config' => 'yamlStyle, fixedPos, runInDebug, collapsed',
                ),
                array(
                    'class' => 'CProfileLogRoute',
                    'levels' => 'profile',
                    'enabled' => true,
                ),
            ),
        ),
    ),
), $commonMainConfig);