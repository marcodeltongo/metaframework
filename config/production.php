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
            'username' => APP_ID,
            'password' => '',
        ),
    ),
), $commonMainConfig);