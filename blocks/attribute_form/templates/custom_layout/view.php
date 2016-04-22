<?php
use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;

if (empty($aftID)) {
    echo t('No form type selected');
    return;
}

if (!empty($layoutAttributes)) {
?>
<br>
<div class="row">
    <div class="col-xs-12">
        <?php if(isset($errors) && $errors->has()): ?>
            <div class="alert alert-danger">
                <?php $errors->output(); ?>
            </div>
        <?php endif; ?>
        <?php
        if(!empty($message)): ?>
            <div class="alert alert-success">
                <?php if (is_array($message)): ?>
                    <ul>
                        <?php foreach ($message as $msg): ?>
                            <li><?= $msg; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <?= $message; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<form method="post" action="<?= $this->action('layoutSubmit') ?>">
    <input type="hidden" name="aftID" value="<?= $aftID ?>">
    <input type="hidden" name="_token" id="_token" value="<?= $token; ?>"/>

    <?php foreach ($layoutAttributes->formPages as $row => $formPageRow) {
        if($formPageRow){
        ?>

    <div class="row-fluid" data-row-id="<?= $row; ?>">
        <?php foreach ($formPageRow as $col => $formPageCol) {?>
        <div class="<?= $formPageCol->columnClass; ?>" data-column-id="<?= $col; ?>">

            <?php foreach ((array)$formPageCol as $key => $formPage) {

                        ?>

                        <div class="attribute-form-page-attributes">
                            <?php
                            if (is_array($formPage) && !empty($formPage)) {
                                foreach ($formPage as $attribute) {
                                    if($attribute->label){
                                        echo "<label class='control-label'>".$attribute->akName."</label>";
                                    }else {
                                        $attributeObject = AttributeFormKey::getByID($attribute->akID);
                                        ?>
                                        <div class="form-group attribute-row"
                                             id="attribute-key-id-<?= $attributeObject->getAttributeKeyID() ?>">
                                            <div class="">
                                                <?php
                                                if ($attribute->required) {
                                                    echo '<span class="text-danger">*</span>';
                                                }
                                                ?>
                                                <?php $attributeObject->render('form', false); ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                    <?php
            } ?>
        </div>
        <?php } ?>
    </div>

    <?php
    }
    } ?>
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
    <style>
        .attribute-row{
            border:none ;
        }
        .form-control{
            width: 96%;
            float: left;
        }
        .text-danger{
            float: left;
        }

        .no-js #loader {
            display: none;
        }
        .js #loader {
            display: block;
            position: absolute;
            left: 100px;
            top: 0;
        }
        .se-pre-con {
            position: fixed;
            right: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            text-align: center;
            background:  center no-repeat #fff;
        }

        .se-pre-con img{
            margin-top: 25%;
            clear: both;
        }
    </style>
    <?php
}