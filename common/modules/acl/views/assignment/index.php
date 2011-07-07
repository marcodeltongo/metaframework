<?php
$this->breadcrumbs = array(
Yii::t('AclModule', 'acl') => array('/acl'),
    Yii::t('AclModule', 'Auth Assignments') => array('index'),
    Yii::t('AclModule', 'List'),
);
?>

<h1><?php echo Yii::t('AclModule', 'Manage'); ?></h1>

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
			Yii::t('AclModule', 'ID'),
			Yii::t('AclModule', 'Item name'),
			Yii::t('AclModule', 'UserID'),
			Yii::t('AclModule', 'Bizrule'),
			Yii::t('AclModule', 'Data'),

        ),
        'colModel' => array(
			array('name' => Yii::t('AclModule', 'ID'), 'index' => 'id'),
			array('name' => Yii::t('AclModule', 'Item name'), 'index' => 'itemname'),
			array('name' => Yii::t('AclModule', 'UserID'), 'index' => 'userid'),
			array('name' => Yii::t('AclModule', 'Bizrule'), 'index' => 'bizrule'),
			array('name' => Yii::t('AclModule', 'Data'), 'index' => 'data'),

        ),
        'viewrecords' => true,
        'gridview' => true,
        'scroll' => true,
        'caption' => '',
        'width' => 950,
        'height' => 400,
        'forceFit' => true,
        'shrinkToFit' => true,
        'altRows' => true,
        'rownumbers' => true,
        'onSelectRow' => "js:function(id){ if(id){ window.location = '$updateUrl?id='+id; }}",
    ),
    // 'tableOptions' => array(),
    // 'pagerOptions' => array(),
    // 'navBarOptions' => array(),
)); ?></div>


<div class="grid-buttons">
<?php echo CHtml::link(Yii::t('AclModule', 'Create'), $this->createUrl('create'), array('class' => 'create-button')); ?></div>