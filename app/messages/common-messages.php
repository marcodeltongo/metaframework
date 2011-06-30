<?php
/**
 * This is the configuration for generating message translations
 * for the Yii framework. It is used by the 'yiic message' command.
 */
return array(
	'sourcePath' => dirname(__FILE__) . '/../../common',
	'messagePath' => dirname(__FILE__),
	'languages' => array('en','it'),
	'fileTypes' => array('php'),
    'overwrite' => true,
	'exclude' => array(
		'.svn',
		'yiilite.php',
		'yiit.php',
		'/i18n/data',
		'/extensions',
		'/messages',
		'/vendors',
		'/public',
		'/gii',
	),
);
