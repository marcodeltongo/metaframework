<?php

/**
 * UserIdentity
 *
 * Implements a flexible user identity.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class UserIdentity extends CUserIdentity
{
    const ERROR_EMAIL_INVALID   = 11;
    const ERROR_STATUS_INACTIVE = 12;
    const ERROR_STATUS_BANNED   = 13;

    /**
     * User unique ID.
     *
     * @property string
     */
    private $_id;

    /**
     * Returns user unique ID.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Authenticate user.
     *
     * @return boolean
     */
    public function authenticate()
    {
        global $config;
        $model = $config['components']['user']['model'];

        $record = $model::model()->authenticate($this->username, $this->password);

        if ($record instanceof $model) {
            $this->_id = $record->getPrimaryKey();
            $this->errorCode = self::ERROR_NONE;
            
            return true;
        }

        return false;
    }

}