<?php

if (empty($aftID)) {
    echo t('No form type selected');
    return;
}

if (is_array($attributes) && !empty($attributes)) {
    ?>
    <form method="post" action="<?= $this->action('submit') ?>">
        <input type="hidden" name="aftID" value="<?= $aftID ?>">
        <input type="hidden" name="_token" id="_token" value="<?= $token; ?>"/>
        <?php
        foreach ($attributes as $attribute) {
            ?>
            <div class="form-group row attribute-row" id="attribute-key-id-<?= $attribute->getAttributeKeyID() ?>">
                <label
                    class="col-sm-4 control-label"><?= tc('AttributeKeyName', $attribute->getAttributeKeyName()) ?></label>

                <div class="col-sm-8">
                    <?php $attribute->render('form', false); ?>
                </div>
            </div>
        <?php
        }
        ?>
        <input type="submit" class="btn btn-primary">
    </form>
<?php
}