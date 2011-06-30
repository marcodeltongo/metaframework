<?php
$this->breadcrumbs = array(
Yii::t('model_AuthItem', 'acl') => array('/acl'),
    Yii::t('model_AuthItem', 'Auth Items') => array('index'),
    Yii::t('model_AuthItem', 'List'),
);
?>

<h1><?php echo Yii::t('model_AuthItem', 'Manage'); ?></h1>

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
			Yii::t('model_AuthItem', 'Name'),
			Yii::t('model_AuthItem', 'Type'),
			Yii::t('model_AuthItem', 'Description'),

        ),
        'colModel' => array(
			array('name' => Yii::t('model_AuthItem', 'Name'), 'index' => 'name'),
			array('name' => Yii::t('model_AuthItem', 'Type'), 'index' => 'type'),
			array('name' => Yii::t('model_AuthItem', 'Description'), 'index' => 'description'),

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
<?php echo CHtml::link(Yii::t('model_AuthItem', 'Create'), $this->createUrl('create'), array('class' => 'create-button')); ?></div>