<div class="crudform">
<?php $form = $this->beginWidget('ActiveForm', array(
    'id' => 'auth-assignment-form',
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
    ),
    'enableClientValidation' => true,
)); ?>

<?php echo $form->errorSummary($model); ?>

<fieldset class="attributes">

	<div class='auth-assignment-userid attribute row'>
		<?php echo $form->labelEx($model, 'userid'); ?>
		<div class='input text-input'><?php echo $form->textField($model, 'userid', array('size' => 50, 'maxlength' => 255)); ?></div>
		<?php echo $form->error($model, 'userid'); ?>
	</div>

	<div class='auth-assignment-bizrule attribute row'>
		<?php echo $form->labelEx($model, 'bizrule'); ?>
		<div class='input textarea'><?php echo $form->textArea($model, 'bizrule', array('rows' => 8, 'cols' => 50)); ?></div>
		<?php echo $form->error($model, 'bizrule'); ?>
	</div>

	<div class='auth-assignment-data attribute row'>
		<?php echo $form->labelEx($model, 'data'); ?>
		<div class='input textarea'><?php echo $form->textArea($model, 'data', array('rows' => 8, 'cols' => 50)); ?></div>
		<?php echo $form->error($model, 'data'); ?>
	</div>

    <div class="auth-assignment-item relation row">
        <?php echo $form->labelEx($model, 'item'); ?>
        <div class='input relation-input'><?php $this->widget('common.widgets.Related', array(
            'form' => $form,
            'model' => $model,
            'relation' => 'item',
        )); ?></div>
        <?php echo $form->error($model, 'item'); ?>
    </div>

</fieldset>

<div class="row form-buttons">
    <ul>
        <li><?php echo CHtml::submitButton(Yii::t('model_AuthAssignment', 'Save and continue'), array('name' => '_continue')); ?></li>
        <li><?php echo CHtml::submitButton(Yii::t('model_AuthAssignment', 'Save and return'), array('name' => '_save')); ?></li>
        <li><?php echo CHtml::submitButton(Yii::t('model_AuthAssignment', 'Save and create new'), array('name' => '_add')); ?></li>
        <li><?php if (!$model->isNewRecord) { echo CHtml::submitButton(Yii::t('model_AuthAssignment', 'Clone'), array('name' => '_clone')); } ?></li>
        <li><?php if (!$model->isNewRecord) { echo CHtml::submitButton(Yii::t('model_AuthAssignment', 'Delete'), array('name' => '_delete')); } ?></li>
    </ul>
</div>

<?php $this->endWidget(); ?>
</div><!-- form -->