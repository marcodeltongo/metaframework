<?php
$this->breadcrumbs = array(
Yii::t('AclModule', 'acl') => array('/acl'),
    Yii::t('AclModule', 'Auth Assignments') => array('index'),
    Yii::t('AclModule', 'Edit'),
);
?>
<h1><?php echo Yii::t('AclModule', 'Update'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>