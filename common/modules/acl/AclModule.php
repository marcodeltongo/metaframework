<?php

/**
 * ACL Module.
 *
 * Implements DB management forms for Yii DbAuthManager.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class AclModule extends CWebModule
{

    /**
     * Setup our settings.
     */
    public function init()
    {
        $this->defaultController = 'dashboard';

        $this->setImport(array(
                'acl.models.*',
        ));
    }

}
