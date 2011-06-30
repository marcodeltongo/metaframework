<?php
$this->breadcrumbs = array(
Yii::t('model_AuthAssignment', 'acl') => array('/acl'),
    Yii::t('model_AuthAssignment', 'Auth Assignments') => array('index'),
    Yii::t('model_AuthAssignment', 'List'),
);
?>

<h1><?php echo Yii::t('model_AuthAssignment', 'Manage'); ?></h1>

<div class="grid">
<?php
$listDataUrl = $this->createUrl('listData');
$updateUrl = $this->createUrl('update');
$this->widget('common.widgets.grid.jqGrid', array(
    'id' => 'auth-assignment-grid',
    'useNavBar' => true,
    'options' => array(
        'url' => $listDataUrl,
        'datatype' => 'json',
        'mtype' => 'GET',
        'colNames' => array(
			Yii::t('model_AuthAssignment', 'ID'),
			Yii::t('model_AuthAssignment', 'Item name'),
			Yii::t('model_AuthAssignment', 'UserID'),
			Yii::t('model_AuthAssignment', 'Bizrule'),
			Yii::t('model_AuthAssignment', 'Data'),

        ),
        'colModel' => array(
			array('name' => Yii::t('model_AuthAssignment', 'ID'), 'index' => 'id'),
			array('name' => Yii::t('model_AuthAssignment', 'Item name'), 'index' => 'itemname'),
			array('name' => Yii::t('model_AuthAssignment', 'UserID'), 'index' => 'userid'),
			array('name' => Yii::t('model_AuthAssignment', 'Bizrule'), 'index' => 'bizrule'),
			array('name' => Yii::t('model_AuthAssignment', 'Data'), 'index' => 'data'),

        ),
        'viewrecords' => true,
        'gridview' => true,
        'scroll' => true,
        'caption' => '',
        'width' => 950,
        'forceFit' => 'true',
        'shrinkToFit' => 'true',
        'onSelectRow' => "js:function(id){ if(id){ window.location = '$updateUrl?id='+id; }}",
    ),
    // 'tableOptions' => array(),
    // 'pagerOptions' => array(),
    // 'navBarOptions' => array(),
)); ?></div>


<div class="grid-buttons">
<?php echo CHtml::link(Yii::t('model_AuthAssignment', 'Create'), $this->createUrl('create'), array('class' => 'create-button')); ?></div>