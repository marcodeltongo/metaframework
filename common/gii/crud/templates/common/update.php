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
    Yii::t('model_{$this->modelClass}', 'Edit'),
);\n";

echo "?>";
?>

<h1><?php echo "<?php echo Yii::t('model_{$this->modelClass}', 'Update'); ?>"; ?></h1>

<?php echo "<?php echo \$this->renderPartial('_form', array('model' => \$model)); ?>"; ?>