<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="form-group">
            <?= $form->label($controller->field('mailSubject'), t('Subject').'<span class="text-danger required">*</span>'); ?>
            <?= $form->text($controller->field('mailSubject'), $controller->fieldValue('mailSubject')); ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-5">
        <div class="form-group">
            <?= $form->label($controller->field('mailBody'), t('Body').'<span class="text-danger required">*</span>'); ?>
            <?= $form->textarea($controller->field('mailBody'), $controller->fieldValue('mailBody'), array('class'=>'redactor-content')); ?>
        </div>
    </div>
</div>