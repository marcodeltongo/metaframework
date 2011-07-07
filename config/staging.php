<?php

/*
 * Inherits from common main.
 */
$commonMainConfig = require(ROOT_DIR . 'config/main.php');

/**
 * Common development configuration.
 */
return CMap::mergeArray(array(
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=' . APPSUITE_ID,
            'username' => 'root',
            'password' => '',
            'enableProfiling' => true,
            'enableParamLogging' => true,
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CProfileLogRoute',
                    'levels' => 'profile',
                    'enabled' => true,
                ),
            ),
        ),
    ),
), $commonMainConfig);
