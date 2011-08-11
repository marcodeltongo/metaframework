<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */

echo "<?php\n";

$label = $this->pluralize($this->class2name($this->modelClass));

$baseCrumb = '';
$basePart = str_part($this->controller, '/');
if ($basePart !== $this->controller) {
    $baseCrumb = "\nYii::t('model_{$this->modelClass}', '$basePart') => array('/$basePart'),";
}

echo "\$this->breadcrumbs = array($baseCrumb
    Yii::t('model_{$this->modelClass}', '$label') => array('index'),
    Yii::t('model_{$this->modelClass}', 'List'),
);\n";

$modelClass = $this->modelClass;
$relations = $modelClass::model()->relations();
$related = array_key_from_value($relations, 2, false, 'name');
$HtmlId = $this->class2id($this->modelClass);

echo "?>\n";
?>

<h1><?php echo "<?php echo Yii::t('model_{$this->modelClass}', 'Manage'); ?>"; ?></h1>

<div class="grid">
<?php
$colNames = '';
$colModel = '';
$labels = $modelClass::model()->attributeLabels();
$gridCols = $modelClass::model()->gridAttributes();
foreach($gridCols as $column) {
    $label = "Yii::t('model_{$modelClass}', '" . $labels[$column] . "')";
    $colNames .= "\t\t\t{$label},\n";
    $colModel .= "\t\t\tarray('name' => '{$column}', 'index' => '{$column}'),\n";
}
echo <<<EOGRID
<?php
\$listDataUrl = \$this->createUrl('listData');
\$updateUrl = \$this->createUrl('update');
\$this->widget('common.widgets.grid.jqGrid', array(
    'id' => '{$HtmlId}-grid',
    'useNavBar' => true,
    'options' => array(
        'url' => \$listDataUrl,
        'datatype' => 'json',
        'mtype' => 'GET',
        'colNames' => array(
$colNames
        ),
        'colModel' => array(
$colModel
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
        'onSelectRow' => "js:function(id){ if(id){ window.location = '\$updateUrl?id='+id; }}",
    ),
    // 'tableOptions' => array(),
    // 'pagerOptions' => array(),
    // 'navBarOptions' => array(),
)); ?>
EOGRID;
?>
</div>

<?php /*
echo "<?php"; ?> $this->widget('zii.widgets.grid.CGridView', array(
    'id' => '<?php echo $this->class2id($this->modelClass); ?>-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}',
        ),
<?php
foreach($this->tableSchema->columns as $column) {
    if ($column->autoIncrement or $column->name == 'password' or $column->name == 'extra') {
        continue;
    }
    $dbType = strtoupper($column->dbType);
    if (array_key_exists($column->name, $related)) {
        echo "\t\t'{$column->name}' => array(
            'name' => '{$column->name}',
            'value' => '\$data->{$related[$column->name]['name']}->toString()',
            'filter' => Helper::listData({$related[$column->name][1]}::model()->findAll(), '{$related[$column->name][2]}', '{$related[$column->name][2]}', null, ' | '),
        ),\n";
    } else {

        echo "\t\t'" . $column->name . "',\n";
    }
}
?>
        array(
            'class' => 'CButtonColumn',
            'template' => '{delete}',
        ),
    ),
));
// */?>

<div class="grid-buttons">
<?php echo "<?php echo CHtml::link(Yii::t('model_{$this->modelClass}', 'Create'), \$this->createUrl('create'), array('class' => 'create-button')); ?>"; ?>
</div>