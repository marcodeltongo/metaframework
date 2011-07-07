<?php

/**
 * Application main configuration.
 */
return array(
    'modules'=>array(
        'acl' => array (
            'class' => 'common.modules.acl.aclModule',
        ),
    ),
    'components' => array(
        'user' => array(
            'allowAutoLogin' => true,
            'loginUrl' => array('user/login'),
            'class' => 'common.components.AppUser',
            'model' => 'User',
        ),
        'message' => array(
            'forceTranslation' => true,
        ),
        /* Override Yii translations (see http://www.yiiframework.com/wiki/18/):
        'coreMessages'=>array(
            'basePath' => APP_DIR . 'messages',
        ),
        //*/
    ),
    'params' => array(
    ),
);