<?php
$this->breadcrumbs = array(
Yii::t('model_AuthAssignment', 'acl') => array('/acl'),
    Yii::t('model_AuthAssignment', 'Auth Assignments') => array('index'),
    Yii::t('model_AuthAssignment', 'New'),
);
?>
<h1><?php echo Yii::t('model_AuthAssignment', 'Create'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>