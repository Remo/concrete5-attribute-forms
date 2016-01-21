<?php print Core::make('helper/concrete/ui')->tabs(array(
	array('atform-general', t('General'), true),
	array('atform-options', t('Options')),
));?>
<div class="ccm-tab-content" id="ccm-tab-content-atform-general">
    <fieldset>
        <legend><?= t('Form Type')?></legend>
        <div class="form-group" >
            <?= $form->select('aftID', $formTypes, $aftID);?>
        </div>
    </fieldset>
</div>
<div class="ccm-tab-content" id="ccm-tab-content-atform-options">
    <fieldset>
        <div class="form-group">
            <?= $form->label('submitText', t('Submit Text'))?>
            <?= $form->text('submitText', $submitText)?>
        </div>
        <div class="form-group">
            <?= $form->label('thankyouMsg', t('Message to display when completed'))?>
            <?= $form->textarea('thankyouMsg', $thankyouMsg, array('rows' => 3))?>
        </div>
        <div class="form-group">
            <?= $form->label('recipientEmail', t('Notify me by email when people submit this form'))?>
            <div class="input-group">
                <span class="input-group-addon" style="z-index: 2000">
                <?= $form->checkbox('notifyMeOnSubmission', 1, $notifyMeOnSubmission == 1, array('onclick' => "$('input[name=recipientEmail]').focus()"))?>
                </span><?= $form->text('recipientEmail', $recipientEmail, array('style' => 'z-index:2000;' ))?>
            </div>
            <span class="help-block"><?= t('(Seperate multiple emails with a comma)')?></span>
        </div>
        <div class="form-group">
            <label class="control-label">
                <?= $form->checkbox('notifySubmitor', 1, $notifySubmitor); ?>
                <span><?= t('Notify submitor.'); ?></span>
            </label>
        </div>
        <div class="form-group">
            <label class="control-label"><?= t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', t('http://en.wikipedia.org/wiki/Captcha'))?></label>
            <div class="radio">
                <label>
                    <?= $form->radio('displayCaptcha', 1, (int) $displayCaptcha); ?>
                    <span><?= t('Yes')?></span>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?= $form->radio('displayCaptcha', 0, (int) $displayCaptcha); ?>
                    <span><?= t('No')?></span>
                </label>
            </div>
        </div>
    </fieldset>
</div>