
<fieldset>
    <legend><?php echo t('Form Type')?></legend>
        <div class="form-group" >
            <?= $form->select('aftID', $formTypes, $aftID);?>
        </div>
</fieldset>
<fieldset>
    <legend><?php echo t('Notfications')?></legend>
    <div class="form-group">
        <label class="control-label">
            <?= $form->checkbox('notifyMeOnSubmission', 1, $notifyMeOnSubmission); ?>
            <span><?= t('Notify me on submission.'); ?></span>
        </label>
    </div>
    <div class="form-group">
        <label class="control-label">
            <?= $form->checkbox('notifySubmitor', 1, $notifySubmitor); ?>
            <span><?= t('Notify submitor.'); ?></span>
        </label>
    </div>
</fieldset>