<?php
$this->breadcrumbs = array(
Yii::t('model_AuthItem', 'acl') => array('/acl'),
    Yii::t('model_AuthItem', 'Auth Items') => array('index'),
    Yii::t('model_AuthItem', 'New'),
);
?>
<h1><?php echo Yii::t('model_AuthItem', 'Create'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>