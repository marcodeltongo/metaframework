<?php

/**
 * ActiveRecordLogBehavior class.
 *
 * Logs CRUD actions to db table.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class ActiveRecordLogBehavior extends CActiveRecordBehavior
{
    /**
     * The attributes copy
     *
     * @var array
     */
    private $__oldAttributes = array();

    /**
     * Checks if attributes changed since last action.
     *
     * @return boolean
     */
    public function changed()
    {
        foreach ($this->Owner->getAttributes() as $key => $value) {
            if (!array_key_exists($key, $this->__oldAttributes)
                    or ($value !== $this->__oldAttributes[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Responds to {@link CModel::onAfterSave} event.
     *
     * @param CModelEvent event
     */
    public function afterSave($event)
    {
        if (!$this->changed()) {
            return;
        }

        $owner = $this->Owner;
        $log = new ActiveRecordLog;
        $log->table = $owner->tableName();
        $log->model = get_class($owner);
        $log->model_pk = $owner->getPrimaryKey();
        $log->user_pk = (!Yii::app()->user->getIsGuest()) ? Yii::app()->user->getId() : null;
        $log->browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $log->host_info = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $log->host_info .= isset($_SERVER['REMOTE_HOST']) ? ' - ' . $_SERVER['REMOTE_HOST'] : '';
        if ($owner->isNewRecord) {
            /*
             * INSERT
             */
            $log->action = ActiveRecordLog::ACTION_INSERT;
            $log->before = null;
            $log->after = $owner->getAttributes();
        } else {
            /*
             * UPDATE
             */
            $log->action = ActiveRecordLog::ACTION_UPDATE;
            $log->before = $this->__oldAttributes;
            $log->after = $owner->getAttributes();
        }
        $log->save();

        $this->__oldAttributes = $this->Owner->getAttributes();
    }

    public function afterDelete($event)
    {
        $owner = $this->Owner;
        $log = new ActiveRecordLog;
        $log->action = ActiveRecordLog::ACTION_DELETE;
        $log->user_pk = (!Yii::app()->user->getIsGuest()) ? Yii::app()->user->getId() : null;
        $log->browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $log->host_info = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $log->host_info .= isset($_SERVER['REMOTE_HOST']) ? ' - ' . $_SERVER['REMOTE_HOST'] : '';
        $log->table = $owner->tableName();
        $log->model = get_class($owner);
        $log->model_pk = $owner->getPrimaryKey();
        $log->before = $this->__oldAttributes;
        $log->after = null;
        $log->save();
    }

    public function afterFind($event)
    {
        $this->__oldAttributes = $this->Owner->getAttributes();
    }

}