<?php

/*
 * Inherits from application main.
 */
$appMainConfig = require APP_DIR . 'config/main.php';

/**
 * Application development configuration.
 */
return CMap::mergeArray(array(
	'components' => array(
		'message' => array(
			'forceTranslation' => true,
            'cachingDuration' => 86400,
		),
    ),
	'params' => array(
            'google-analytics' => false, // Change to your site's ID code "UA-XXXXX-X" to activate.
	),
), $appMainConfig);