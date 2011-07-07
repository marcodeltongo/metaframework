<?php
$this->breadcrumbs = array(
Yii::t('AclModule', 'acl') => array('/acl'),
    Yii::t('AclModule', 'Auth Items') => array('index'),
    Yii::t('AclModule', 'List'),
);
?>

<h1><?php echo Yii::t('AclModule', 'Manage'); ?></h1>

<div class="grid">
<?php
$listDataUrl = $this->createUrl('listData');
$updateUrl = $this->createUrl('update');
$this->widget('common.widgets.grid.jqGrid', array(
    'id' => 'auth-item-grid',
    'useNavBar' => true,
    'options' => array(
        'url' => $listDataUrl,
        'datatype' => 'json',
        'mtype' => 'GET',
        'colNames' => array(
			Yii::t('AclModule', 'Name'),
			Yii::t('AclModule', 'Type'),
			Yii::t('AclModule', 'Description'),

        ),
        'colModel' => array(
			array('name' => Yii::t('AclModule', 'Name'), 'index' => 'name'),
			array('name' => Yii::t('AclModule', 'Type'), 'index' => 'type'),
			array('name' => Yii::t('AclModule', 'Description'), 'index' => 'description'),

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