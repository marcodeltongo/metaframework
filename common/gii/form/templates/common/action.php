<?php
/**
 * This is the template for generating the action script for the form.
 * - $this: the CrudCode object
 */

$viewName = basename($this->viewName);
?>
public function action<?php echo ucfirst(trim($viewName,'_')); ?>()
{
    $model = new <?php echo $this->modelClass; ?><?php echo empty($this->scenario) ? '' : "('{$this->scenario}')"; ?>;

    /*
     * Ajax-based validation
     */
    $this->performAjaxValidation($model);

    if (isset($_POST['<?php echo $this->modelClass; ?>']))
    {
        $model->attributes = $_POST['<?php echo $this->modelClass; ?>'];

        if($model->validate())
        {
            $this->setFlashSuccess(Yii::t('flash_messages', 'success'));
            return;
        } else {
            $this->setFlashError(Yii::t('flash_messages', 'error'));
        }
    }

    $this->render('<?php echo $viewName; ?>', array('model' => $model));
}
