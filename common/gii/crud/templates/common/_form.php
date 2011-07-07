<?php
/**
 * Crud form template
 *
 * The following variables are available in this template:
 * - $this: the CrudCode object
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */

/**
 * Generates an input field
 *
 * @param CDbColumnSchema $column
 */
function generateAttributeRow($column, $modelClass) {
    $dbType = strtoupper($column->dbType);
    $title = "'title' => \$model->attributeTitle('{$column->name}')";

    echo "\t<div class='{$modelClass}-{$column->name} attribute row'>\n";
    echo "\t\t<?php echo \$form->labelEx(\$model, '{$column->name}'); ?>\n";

    if (in_array($dbType, array('BIT', 'BOOL', 'BOOLEAN', 'TINYINT(1)'))) {
        // BOOLEAN
        echo "\t\t<div class='input boolean-input'><?php echo \$form->dropDownList(\$model, '{$column->name}', array(Yii::t('yii', 'No'), Yii::t('yii', 'Yes')), array($title, 'class' => 'boolean', 'empty' => '')); ?></div>\n";
    } elseif (false !== stripos($dbType, 'TEXT')) {
        if (in_array($column->name, array('extra'))) {
            // EXTRA SERIALIZED VALUES
            echo "\t\t<!--<div class='input extra-input'><?php // echo \$form->textField(\$model, '{$column->name}[\"EXTRAFIELDNAME\"]', array($title, 'size' => 50)); ?></div>-->\n";
        } else {
            // TEXTAREA
            echo "\t\t<div class='input textarea'><?php echo \$form->textArea(\$model, '{$column->name}', array($title, 'rows' => 8, 'cols' => 50)); ?></div>\n";
        }
    } elseif (in_array($dbType, array('DATE', 'DATETIME', 'TIME', 'TIMESTAMP'))) {
        $mode = ($dbType == 'TIMESTAMP') ? 'datetime' : strtolower($dbType);
        echo "\t\t<div class='input datetime-input'><?php \$this->widget('common.extensions.CJuiDateTimePicker.CJuiDateTimePicker', array(\n";
        echo "\t\t\t'model' => \$model,\n";
        echo "\t\t\t'attribute' => '{$column->name}',\n";
        echo "\t\t\t'mode' => '$mode',\n";
        echo "\t\t\t'options' => array(),\n";
        echo "\t\t\t'htmlOptions' => array($title),\n";
        echo "\t\t)); ?></div>\n";
    } elseif (substr($dbType, 0, 4) == 'ENUM') {
        echo sprintf("\t\t<div class='input enum-input'><?php echo CHtml::activeDropDownList(\$model, '%s', array(\n", $column->name);

        $enum_values = explode(',', substr($column->dbType, 4, strlen($column->dbType) - 1));
        foreach ($enum_values as $value) {
            $value = trim($value, "()'");
            echo "\t\t\t'$value' => Yii::t('app', '" . $value . "') ,\n";
        }
        echo "\t\t)); ?>\n";
    } elseif (false !== stripos($column->name, 'image')) {
      echo "\t\t<div class='input image-input'><?php echo \$form->imageField(\$model, '{$column->name}', array($title)); ?></div>\n";
    } else {
        if (in_array($column->name, array('password', 'pass', 'passwd', 'passcode'))) {
            $inputField = 'passwordField';
        } else {
            $inputField = 'textField';
        }

        if ($column->type !== 'string' or $column->size === null) {
            echo "\t\t<div class='input text-input'><?php echo \$form->{$inputField}(\$model, '{$column->name}', array($title, 'size' => 50)); ?></div>\n";
        } else {
            $size = (($maxLength = $column->size) < 50) ? $column->size : 50;
            echo "\t\t<div class='input text-input'><?php echo \$form->{$inputField}(\$model, '{$column->name}', array($title, 'size' => $size, 'maxlength' => $maxLength)); ?></div>\n";
        }
    }

    echo "\t\t<?php echo \$form->error(\$model, '{$column->name}'); ?>\n";
    echo "\t</div>\n\n";
}

/**
 * Prepare informations
 */
// $modelClass = str_rpart(str_part($this->model, '.', false), '.', false);
$modelClass = $this->modelClass;
$modelHtmlId = $this->class2id($this->modelClass);
$relations = $modelClass::model()->relations();
$excludedFileds = array_key_from_value($relations, 2);
?>
<div class="crudform">
<?php echo "<?php \$form = \$this->beginWidget('ActiveForm', array(
    'id' => '" . $this->class2id($this->modelClass) . "-form',
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
    ),
    'enableClientValidation' => true,
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data',
    ),
)); ?>\n"; ?>

<?php echo "<?php echo \$form->errorSummary(\$model); ?>\n"; ?>

<fieldset class="attributes">

<?php
foreach ($this->tableSchema->columns as $column)
{
    if ($column->autoIncrement or array_key_exists($column->name, $excludedFileds)) {
        continue;
    }

    generateAttributeRow($column, $modelHtmlId);
}
?>
</fieldset>

<fieldset class="relations">

<?php
foreach ($relations as $related => $relation)
{
    if ($relation[0] == 'CStatRelation') {
        continue;
    }

    $attributeName = $relation[2];
    if (!in_array($relation[0], array('CBelongsToRelation', 'CHasOneRelation'))) {
        $attributeName = $related;
    }
?>
    <div class="<?php echo "{$modelHtmlId}-{$related}"; ?> relation row">
        <?php echo "<?php echo \$form->labelEx(\$model, '$attributeName'); ?>\n"; ?>
        <?php echo "<div class='input relation-input'><?php \$this->widget('common.widgets.Related', array(
            'form' => \$form,
            'model' => \$model,
            'relation' => '$related',
            'htmlOptions' => array(
                'title' => \$model->attributeTitle('{$related}'),
            ),
        )); ?></div>\n"; ?>
        <?php echo "<?php echo \$form->error(\$model, '{$attributeName}'); ?>\n"; ?>
    </div>

<?php
}
?>
</fieldset>

<div class="row form-buttons">
    <ul>
        <li><?php echo "<?php echo CHtml::submitButton(Yii::t('model_{$this->modelClass}', 'Save and continue'), array('name' => '_continue')); ?>"; ?></li>
        <li><?php echo "<?php echo CHtml::submitButton(Yii::t('model_{$this->modelClass}', 'Save and return'), array('name' => '_save')); ?>"; ?></li>
        <li><?php echo "<?php echo CHtml::submitButton(Yii::t('model_{$this->modelClass}', 'Save and create new'), array('name' => '_add')); ?>"; ?></li>
        <li><?php echo "<?php if (!\$model->isNewRecord) { echo CHtml::submitButton(Yii::t('model_{$this->modelClass}', 'Clone'), array('name' => '_clone')); } ?>"; ?></li>
        <li><?php echo "<?php if (!\$model->isNewRecord) { echo CHtml::submitButton(Yii::t('model_{$this->modelClass}', 'Delete'), array('name' => '_delete')); } ?>"; ?></li>
    </ul>
</div>

<?php echo "<?php \$this->endWidget(); ?>\n"; ?>
</div><!-- form -->
