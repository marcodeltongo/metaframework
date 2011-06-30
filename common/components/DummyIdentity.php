<?php

/**
 * DummyIdentity
 *
 * Implements a very dummy user identity. Mainly useful in early development stages.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class DummyIdentity extends CUserIdentity
{

    /**
     * Authenticates an 'admin' user with 'admin' password.
     *
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        $users = array(
                // username => password
                'admin' => 'admin',
                'editor' => 'editor',
                'user' => 'user',
        );

        if (!isset($users[$this->username])) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif ($users[$this->username] !== $this->password) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->errorCode = self::ERROR_NONE;
        }

        return (self::ERROR_NONE === $this->errorCode);
    }

}