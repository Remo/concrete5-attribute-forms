<?php
use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;

if (empty($aftID)) {
    echo t('No form type selected');
    return;
}
if ($formPage) {
?>
<br>
<?php $this->inc('elements/header.php'); ?>
<form id="attribute-form-block-b<?=$bID;?>" method="post" action="" data-destroy="false">
    <input type="hidden" name="af_token" id="af_token" value="<?= $token; ?>"/>
    <input type="hidden" name="formPageHandle" value="<?= $formPage->handle; ?>"/>
    <div class="attribute-form-page">
        <h2><?= $formPage->name ?></h2>

        <div class="attribute-form-page-attributes">
            <?php
            if (is_array($formPage->attributes) && !empty($formPage->attributes)) {
                foreach ($formPage->attributes as $attribute) {
                    $attributeObject = AttributeFormKey::getByID($attribute->akID); ?>
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
            <div class="clearfix">
                <div class="spacer-row-1"></div>
                <?php if($prevFormPage): ?>
                <input type="submit" name="previousBtn" class="btn btn-default" value="<?= t('Previous'); ?>"
                   onclick="javascript:form.action='<?= $this->action('step_submit', $prevFormPage->handle); ?>';" />
                <?php endif; ?>
                <input type="submit" name="Submit" class="btn btn-default pull-right"
                       value="<?= $nextFormPage ? t('Next') : h(t($submitText)); ?>"
                       onclick="javascript:form.action='<?= $nextFormPage ? $this->action('step_submit', $nextFormPage->handle) : $this->action('step_submit', 'complete'); ?>';"
                   />
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(function(){
        <?php if(isset($message)): ?>
            for ( var i = localStorage.length - 1; i >= 0; i-- ) {
                if ( 'undefined' === typeof Array.indexOf && -1 !== localStorage.key(i).indexOf('garlic:attribute-form-block-b<?=$bID;?>:') ) {
                  localStorage.removeItem( localStorage.key(i) );
                }
            }
        <?php endif; ?>
        $('#attribute-form-block-b<?=$bID;?>').garlic({
            excluded:'input[type="file"], input[type="hidden"], input[type="submit"]',
            onRetrieve: function ( elem, retrievedValue ) {
                if(elem.hasClass('hasDatepicker')){
                    var id = elem.attr('id').replace("_pub", "");
                    $('#'+id).val(retrievedValue);
                }
            },
            getPath: function ( $elem ) {
                return 'garlic:attribute-form-block-b<?=$bID;?>:'+$elem.attr( 'id' );
            }
        });
        if ($('.alert').is(':visible')) {
            window.scrollTo(0, $(".alert").offset().top);
        }
    });
</script>
    <?php
}