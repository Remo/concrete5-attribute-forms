<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<form role="role" class="form-horizontal form-groups-bordered" method="post"
      action="<?= $view->action('save', isset($attributeForm) ? $attributeForm->getID() : false) ?>">

    <div class="panel panel-default panel-shadow">
        <div class="panel-heading">
            <div class="panel-title">
                <?= isset($attributeForm) ? t('Edit Form') : t('Add Form') ?>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?= t('Form Name') ?>
                        </label>

                        <div class="col-sm-10">
                            <?= $form->text('formName',
                                            isset($attributeForm) ? $attributeForm->getFormName() : '',
                                            array('class' => 'form-control')) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?= t('Attributes') ?>
                        </label>

                        <div class="col-sm-10" id="form-attributes">
                            <?php foreach ($attributes as $attribute) { ?>
                                <div class="checkbox"
                                     data-order="<?= isset($selectedAttributes) && in_array($attribute->getAttributeKeyID(),
                                         $selectedAttributes) ? (array_search($attribute->getAttributeKeyID(),
                                         $selectedAttributes)) : 999999 ?>">
                                    <label>
                                        <input class="no-icheck" type="checkbox" name="attributes[]"
                                               value="<?= $attribute->getAttributeKeyID() ?>" <?= isset($selectedAttributes) && in_array($attribute->getAttributeKeyID(),
                                            $selectedAttributes) ? 'checked="checked"' : '' ?>>
                                        <?= $attribute->getAttributeKeyName() ?>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn btn-blue"><?= t('Save') ?></button>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function () {
        // make attribute list sortable
        $("#form-attributes").sortable();

        $('.ccm-advanced-editor').redactor({
            'plugins': ['concrete5']
        });

        // sort selected attributes by data attribute
        var $wrapper = $('#form-attributes');
        $wrapper.find('div').sort(function (a, b) {
            return +a.dataset.order - +b.dataset.order;
        }).appendTo($wrapper);
    });
</script>