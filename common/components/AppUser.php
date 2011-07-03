<?php

/**
 * AppUser
 *
 * Implements a flexible user.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class AppUser extends CWebUser
{
    /**
     * Model class.
     *
     * @property object
     */
    public $model;

    /**
     * Model instance.
     *
     * @property object
     */
    private $record;

    /**
     * Load user model.
     *
     * @param string $id
     * @return object
     */
    protected function load($id = null)
    {
        if ($this->record === null) {
            $id = ($id === null) ? Yii::app()->user->id : $id;
            $class = $this->model;
            $this->record = $class::model()->findByPk($id);
        }

        return $this->record;
    }

    /**
     * Return user model.
     *
     * @return object
     */
    public function getRecord()
    {
        return $this->load();
    }

    /**
     * Alias for CWebUser::checkAccess
     *
     * @see CWebUser::checkAccess
     */
    public function can($operation, $params = array(), $allowCaching = true)
    {
        /*
         * If array, iterate on items.
         */
        if (is_array($operation)) {
            foreach ($operation as $item) {
                if ($this->can($item)) {
                    return true;
                }
            }
            return false;
        }
        /*
         * Check requested first.
         */
        if ($this->checkAccess($operation, $params, $allowCaching)) {
            return true;
        }
        /*
         * Check superuser permissions.
         */
        if (!$this->checkAccess('*', array(), $allowCaching) and (false !== strpos($operation, '.'))) {
            /*
             * Go up the permissions tree.
             */
            $op = $operation;
            while ($op = strpbrk($op, '.')) {
                if ($this->checkAccess('*' . $op, array(), $allowCaching)) {
                    return true;
                }
                $op = substr($op, 1);
            }
        } else {
            return true;
        }

        return false;
    }

    /**
     * Alias for CWebUser::checkAccess
     *
     * @see CWebUser::checkAccess
     */
    public function role($operation, $params = array(), $allowCaching = true)
    {
        return $this->checkAccess($operation, $params, $allowCaching);
    }

}
