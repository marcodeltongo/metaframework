<?php
$this->pageTitle = Yii::app()->name . ' - Login';
$this->breadcrumbs = array(
    Yii::t('login', 'Login'),
);
?>

<h1><?php echo Yii::t('login', 'Login'); ?></h1>

<div class="crudform">
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'login-form',
    'enableAjaxValidation' => true,
));
?>

<fieldset>

<div class="row">
    <?php echo $form->labelEx($model, 'username'); ?>
    <div class="input"><?php echo $form->textField($model, 'username'); ?></div>
    <?php echo $form->error($model, 'username'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model, 'password'); ?>
    <div class="input"><?php echo $form->passwordField($model, 'password'); ?></div>
    <?php echo $form->error($model, 'password'); ?>
</div>

<div class="row rememberMe">
    <?php echo $form->label($model, 'rememberMe'); ?>
    <div class="input"><?php echo $form->checkBox($model, 'rememberMe'); ?></div>
    <?php echo $form->error($model, 'rememberMe'); ?>
</div>

</fieldset>

<div class="row form-buttons">
    <ul>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li><?php echo CHtml::submitButton('Login', array('name' => '_save')); ?></li>
    </ul>
</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
