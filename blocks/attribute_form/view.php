<?php
use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;

if (empty($aftID)) {
    echo t('No form type selected');
    return;
}

if (!empty($attributes)) {
?>
<br>
<?php $this->inc('elements/header.php'); ?>
<form method="post" action="<?= $this->action('submit') ?>">
    <input type="hidden" name="aftID" value="<?= $aftID ?>">
    <input type="hidden" name="af_token" id="af_token" value="<?= $token; ?>"/>

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
                                <?= $attributeObject->getAttributeKeyDisplayName(); ?>
                                <?php
                                if($attribute->required){
                                    echo '<span class="text-danger">*</span>';
                                }
                                ?>
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
    <?php  if ($captcha): ?>
    <div class="row">
        <div class="col-sm-offset-4 col-sm-8">
            <div class="form-group captcha">
                <?php
                $captchaLabel = $captcha->label();
                if (!empty($captchaLabel)):?>
                    <label class="control-label"><?= $captchaLabel; ?></label>
                <?php
                endif; ?>
                <div><?php  $controller->diaplayCaptcha($captcha, $aftID); ?></div>
                <div><?php  $captcha->showInput(); ?></div>
            </div>
        </div>
    </div>
    <?php  endif; ?>
    <div class="row">
        <div class="col-sm-offset-4 col-sm-8">
            <span class="small text-muted">* <?=t('Required fields.');?></span>
            <input type="submit" name="Submit" class="btn btn-primary pull-right" value="<?= h(t($submitText)); ?>" />
        </div>
    </div>
</form>
    <?php
}