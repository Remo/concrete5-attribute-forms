<?php
use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;

if (empty($aftID)) {
    echo t('No form type selected');
    return;
}

if (!empty($attributes)) {
    ?>
    <form method="post" action="<?= $this->action('submit') ?>">
        <input type="hidden" name="aftID" value="<?= $aftID ?>">
        <input type="hidden" name="_token" id="_token" value="<?= $token; ?>"/>

        <?php foreach ($attributes->formPages as $formPage) { ?>
            <div class="attribute-form-page">
                <h2><?= $formPage->name ?></h2>

                <div class="attribute-form-page-attributes">
                    <?php
                    if (is_array($formPage->attributes) && !empty($formPage->attributes)) {
                        foreach ($formPage->attributes as $attribute) {
                            
                            $attributeObject = AttributeFormKey::getByID($attribute->akID);
                            ?>
                            <div class="form-group row attribute-row" id="attribute-key-id-<?= $attributeObject->getAttributeKeyID() ?>">
                                <label class="col-sm-4 control-label">
                                    <?= tc('AttributeKeyName', $attributeObject->getAttributeKeyName()) ?>
                                </label>
                                <div class="col-sm-8">
                                    <?php $attributeObject->render('form', false); ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        <?php } ?>

        <input type="submit" class="btn btn-primary">
    </form>
    <?php
}