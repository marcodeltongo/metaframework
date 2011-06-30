<?php

/**
 * CrudController is the customized base controller class for CRUD operations.
 *
 * All CRUD controllers for this application should extend from this base class.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
abstract class CrudController extends AclController
{
    /**
     * Managed model class
     *
     * @var string
     */
    protected $modelClass;
    /**
     * Data model instance
     *
     * @var ActiveRecord
     */
    public $model;

    /**
     * Specifies the access control rules.
     *
     * @return array access control rules
     */
    public function accessRules()
    {
        if ($this->aclTurnedOff) {
            return array();
        }

        return array(
                array('allow',
                        'actions' => array('index', 'create', 'update', 'clone', 'delete', 'listData'),
                        'users' => array('@'), # only authenticated users
                ),
                array('deny'),
        );
    }

    /**
     * Generic AJAX validator.
     */
    protected function performAjaxValidation()
    {
        if (Yii::app()->request->isAjaxRequest and isset($_POST['ajax'])) {
            if (isset($_POST['attributes']) and is_array($_POST['attributes'][$this->modelClass])) {
                $attributes = $_POST['attributes'][$this->modelClass];
            } else {
                $attributes = null;
            }
            echo $this->model->validate($attributes);
            Yii::app()->end();
        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @return ActiveRecord
     */
    public function loadModel($id)
    {
        if ((null === $this->model) or ($id !== $this->model->getPrimaryKey())) {
            $class = $this->modelClass;
            $this->model = $class::model()->findByPk($id);

            if (null === $this->model) {
                throw new CHttpException(404, 'The requested page does not exist.');
            }
        }
        return $this->model;
    }

    /**
     * Redirect after editing model data.
     *
     * @param mixed $id
     */
    protected function redirectUser($id = false)
    {
        if (isset($_POST['_returnUrl'])) {
            # Use form field _returnUrl
            $this->redirect($_POST['_returnUrl']);
        } elseif (isset($_GET['_returnUrl'])) {
            # Use urlencoded _returnUrl query parameter
            $this->redirect(urldecode($_GET['_returnUrl']));
        } elseif (isset($_POST['_save'])) {
            # Go to list
            $this->redirect($this->createUrl('index'));
        } elseif (isset($_POST['_add'])) {
            # Create another
            $this->redirect($this->createUrl('create'));
        } elseif (isset($_POST['_continue']) and (false !== $id)) {
            # Re-open saved
            $this->redirect($this->createUrl('update', array('id' => $id)));
        } elseif (isset($_POST['_clone']) and (false !== $id)) {
            # Create another cloning attributes from this
            $this->redirect($this->createUrl('clone', array('id' => $id)));
        }
        # Go to index
        $this->redirect($this->createUrl('index'));
    }

    // ------------------------------------------------------------------------

    /**
     * Manages all models.
     */
    public function actionIndex()
    {
        $this->model = new $this->modelClass('search');

        $this->model->unsetAttributes();
        if (isset($_GET[$this->modelClass])) {
            $this->model->setAttributes($_GET[$this->modelClass]);
        }

        $this->render('index', array('model' => $this->model));
    }

    /**
     * List models as JSON
     *
     * @param bool $_search
     * @param int $nd
     * @param int $page Get the requested page. By default grid sets this to 1
     * @param int $rows Get how many rows we want to have into the grid
     * @param int $sidx Get index row - i.e. user click to sort.
     * @param string $sord Sorting order
     */
    public function actionListData($_search, $nd, $page = 1, $rows = 20, $sidx ='1', $sord = 'asc')
    {
        $this->model = new $this->modelClass('search');
        $page = (int) $page;
        $rows = (int) $rows;
        $offset = ($page == 1) ? 0 : $rows * ($page - 1);
        $cols = $this->model->gridAttributes();
        $total = $this->model->count();

        while (($offset > 0) and ($offset >= $total)) {
            if (--$page == 1) {
                $offset = 0;
                break;
            }
            $offset = $rows * (--$page);
        }

        $criteria = new CDbCriteria;
        $criteria->limit = $rows;
        $criteria->offset = $offset;
        $criteria->select = array_untrim($cols, '`');;
        $records = $this->model->findAll($criteria);

        $rows = array();
        foreach ($records as $record) {
            $cell = array();
            foreach ($cols as $col) {
                $cell[] = $record->$col;
            }
            $rows[] = array('id' => $record->getPrimaryKey(), 'cell' => $cell);
        }

        $data = array(
                'total' => $total,
                'page' => $page,
                'records' => count($records),
                'rows' => $rows,
        );

        echo CJSON::encode($data);
    }

    /**
     * Creates a new model.
     */
    public function actionCreate()
    {
        $this->model = new $this->modelClass;

        /*
         * Ajax-based validation
         */
        $this->performAjaxValidation();


        /*
         * Insert
         */
        if (isset($_POST[$this->modelClass])) {
            $this->model->setAttributes($_POST[$this->modelClass]);

            if ($this->model->save()) {
                $this->setFlashSuccess(Yii::t('flash_messages', 'success'));
                $this->redirectUser($this->model->getPrimaryKey());
            } else {
                $this->setFlashError(Yii::t('flash_messages', 'error'));
            }
        }

        $this->render('create', array('model' => $this->model));
    }

    /**
     * Updates a particular model.
     *
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $this->loadModel($id);

        /*
         * Delete button
         */
        if (isset($_POST['_delete'])) {
            return $this->actionDelete($id);
        }

        /*
         * Ajax-based validation
         */
        $this->performAjaxValidation();

        /*
         * Update
         */
        if (isset($_POST[$this->modelClass])) {
            $this->model->setAttributes($_POST[$this->modelClass]);

            if ($this->model->save()) {
                $this->setFlashSuccess(Yii::t('flash_messages', 'success'));
                $this->redirectUser($this->model->getPrimaryKey());
            } else {
                $this->setFlashError(Yii::t('flash_messages', 'error'));
            }
        }

        $this->render('update', array('model' => $this->model));
    }

    /**
     * Clones a record into a new model.
     *
     * @param integer $id the ID of the model to be updated
     */
    public function actionClone($id)
    {
        if (isset($_POST[$this->modelClass])) {
            $this->actionCreate();
            return;
        }

        $this->loadModel($id);
        $this->model->setPrimaryKey(null);
        $this->model->setIsNewRecord(true);

        $this->render('create', array('model' => $this->model));
    }

    /**
     * Deletes a particular model.
     */
    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirectUser();
        }
    }

}
