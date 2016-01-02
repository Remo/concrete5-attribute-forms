<?php
defined('C5_EXECUTE') or die('Access Denied.');

if (empty($types)) {
    echo t('There are no attribute types available for the attribute form category. Please enable them in the <a href="%s">system settings</a>.', URL::to('/dashboard/system/attributes/types/'));
    return;
}

View::element('dashboard/attributes_table', array('category' => $category, 'attribs'=> $attribs, 'editURL' => '/dashboard/forms/attributes'));
?>

<form method="post" action="<?php echo $view->action('add') ?>">
    <div class="form-group row">
        <label class="col-lg-1 col-md-3 col-sm-4 control-label"><?= $form->label('atID', t('Add Attribute')) ?></label>

        <div class="col-lg-11 col-md-9 col-sm-8 ">
            <?= $form->select('atID', $types) ?>
            <div class="spacer-row-1"></div>
            <div class="btn-toolbar">
                <?= $form->submit('submit', t('Add'), [], 'btn btn-default') ?>
            </div>
        </div>
    </div>
</form>