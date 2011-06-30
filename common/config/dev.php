<?php

/*
 * Inherits from common main.
 */
$commonMainConfig = require COMMON_DIR . 'config/main.php';

/**
 * Common development configuration.
 */
return CMap::mergeArray(array(
	'modules' => array(
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => 'gii',
            'generatorPaths'=>array(
                'common.gii',
            ),
		),
	),
	'components' => array(
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=' . APP_ID,
			'username' => 'root',
			'password' => '',
			'enableProfiling' => true,
			'enableParamLogging' => true,
			'schemaCachingDuration' => '3600',
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
                array(
                    'class' => 'common.extensions.yii-debug-toolbar.YiiDebugToolbarRoute',
                ),
			),
		),
		'urlManager' => array(
			'rules' => array(
				// Gii code generation
				'gii' => 'gii',
				'gii/<controller:\w+>' => 'gii/<controller>',
				'gii/<controller:\w+>/<action:\w+>' => 'gii/<controller>/<action>',
			),
		),
	),
), $commonMainConfig);