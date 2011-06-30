<?php
$this->breadcrumbs = array(
Yii::t('model_AuthAssignment', 'acl') => array('/acl'),
    Yii::t('model_AuthAssignment', 'Auth Assignments') => array('index'),
    Yii::t('model_AuthAssignment', 'Edit'),
);
?>
<h1><?php echo Yii::t('model_AuthAssignment', 'Update'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>