<?php
$this->breadcrumbs = array(
Yii::t('AclModule', 'acl') => array('/acl'),
    Yii::t('AclModule', 'Auth Items') => array('index'),
    Yii::t('AclModule', 'New'),
);
?>
<h1><?php echo Yii::t('AclModule', 'Create'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>