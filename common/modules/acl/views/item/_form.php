<div class="crudform">
<?php $form = $this->beginWidget('ActiveForm', array(
    'id' => 'auth-item-form',
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
    ),
    'enableClientValidation' => true,
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data',
    ),
)); ?>

<?php echo $form->errorSummary($model); ?>

<fieldset class="attributes">

	<div class='auth-item-name attribute row'>
		<?php echo $form->labelEx($model, 'name'); ?>
		<div class='input text-input'><?php echo $form->textField($model, 'name', array('title' => $model->attributeTitle('name'), 'size' => 50, 'maxlength' => 255)); ?></div>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class='auth-item-type attribute row'>
		<?php echo $form->labelEx($model, 'type'); ?>
		<div class='input text-input'><?php echo $form->textField($model, 'type', array('title' => $model->attributeTitle('type'), 'size' => 50)); ?></div>
		<?php echo $form->error($model, 'type'); ?>
	</div>

	<div class='auth-item-description attribute row'>
		<?php echo $form->labelEx($model, 'description'); ?>
		<div class='input textarea'><?php echo $form->textArea($model, 'description', array('title' => $model->attributeTitle('description'), 'rows' => 8, 'cols' => 50)); ?></div>
		<?php echo $form->error($model, 'description'); ?>
	</div>

	<div class='auth-item-bizrule attribute row'>
		<?php echo $form->labelEx($model, 'bizrule'); ?>
		<div class='input textarea'><?php echo $form->textArea($model, 'bizrule', array('title' => $model->attributeTitle('bizrule'), 'rows' => 8, 'cols' => 50)); ?></div>
		<?php echo $form->error($model, 'bizrule'); ?>
	</div>

	<div class='auth-item-data attribute row'>
		<?php echo $form->labelEx($model, 'data'); ?>
		<div class='input textarea'><?php echo $form->textArea($model, 'data', array('title' => $model->attributeTitle('data'), 'rows' => 8, 'cols' => 50)); ?></div>
		<?php echo $form->error($model, 'data'); ?>
	</div>

</fieldset>

<fieldset class="relations">

    <div class="auth-item-assignments relation row">
        <?php echo $form->labelEx($model, 'assignments'); ?>
        <div class='input relation-input'><?php $this->widget('common.widgets.Related', array(
            'form' => $form,
            'model' => $model,
            'relation' => 'assignments',
            'htmlOptions' => array(
                'title' => $model->attributeTitle('assignments'),
            ),
        )); ?></div>
        <?php echo $form->error($model, 'assignments'); ?>
    </div>

    <div class="auth-item-parents relation row">
        <?php echo $form->labelEx($model, 'parents'); ?>
        <div class='input relation-input'><?php $this->widget('common.widgets.Related', array(
            'form' => $form,
            'model' => $model,
            'relation' => 'parents',
            'htmlOptions' => array(
                'title' => $model->attributeTitle('parents'),
            ),
        )); ?></div>
        <?php echo $form->error($model, 'parents'); ?>
    </div>

    <div class="auth-item-children relation row">
        <?php echo $form->labelEx($model, 'children'); ?>
        <div class='input relation-input'><?php $this->widget('common.widgets.Related', array(
            'form' => $form,
            'model' => $model,
            'relation' => 'children',
            'htmlOptions' => array(
                'title' => $model->attributeTitle('children'),
            ),
        )); ?></div>
        <?php echo $form->error($model, 'children'); ?>
    </div>

</fieldset>

<div class="row form-buttons">
    <ul>
        <li><?php echo CHtml::submitButton(Yii::t('AclModule', 'Save and continue'), array('name' => '_continue')); ?></li>
        <li><?php echo CHtml::submitButton(Yii::t('AclModule', 'Save and return'), array('name' => '_save')); ?></li>
        <li><?php echo CHtml::submitButton(Yii::t('AclModule', 'Save and create new'), array('name' => '_add')); ?></li>
        <li><?php if (!$model->isNewRecord) { echo CHtml::submitButton(Yii::t('AclModule', 'Clone'), array('name' => '_clone')); } ?></li>
        <li><?php if (!$model->isNewRecord) { echo CHtml::submitButton(Yii::t('AclModule', 'Delete'), array('name' => '_delete')); } ?></li>
    </ul>
</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
