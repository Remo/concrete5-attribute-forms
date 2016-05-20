<fieldset>
    <legend><?= t('Checkbox Options') ?></legend>

    <div class="form-group">
        <label>
            <?= $form->checkbox('akCheckedByDefault', 1, $akCheckedByDefault) ?>
            <span><?= t('The checkbox will be checked by default.') ?></span>
        </label>
    </div>
    <div class="form-group">
        <label><?= t("Indentation") ?></label>
        <?= $form->number('indentation', $indentation) ?>
    </div>

    <div class="form-group">

        <?php echo $form->label( 'akLabelText', t('Label Text') )?>

        <?php echo $form->text( 'akLabelText' , $akLabelText )?>
    </div>

    <div class="form-group">
        <label><?= t("Attributes") ?></label>

        <?php
        if ($this->getAttributeKey()) {
            $attributeCategoryClass = get_class($this->getAttributeKey());
            $list = $attributeCategoryClass::getList();
            ?>

            <div class="input">
                <table class="table table-bordered table-responsive">
                    <tr>
                        <th><?= t('Attribute') ?></th>
                        <th><?= t('Checked') ?></th>
                        <th><?= t('Unchecked') ?></th>
                    </tr>
                    <?php foreach ($list as $item) {
                        if ($this->getAttributeKey()->getAttributeKeyID() == $item->getAttributeKeyID()) {
                            continue;
                        }
                        ?>
                        <tr>
                            <td><?= $item->getAttributeKeyName() ?></td>
                            <td>
                                <?= $form->select('checkedActions[' . $item->getAttributeKeyID() . ']',
                                    ['none' => t('No Action'), 'hide' => t('Hide'), 'show' => t('Show')],
                                    $akCheckedActions[$item->getAttributeKeyID()]
                                ) ?>
                            </td>
                            <td>
                                <?= $form->select('uncheckedActions[' . $item->getAttributeKeyID() . ']',
                                    ['none' => t('No Action'), 'hide' => t('Hide'), 'show' => t('Show')],
                                    $akUncheckedActions[$item->getAttributeKeyID()]
                                ) ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

        <?php } else { ?>
            <?= t('Please save attribute first, you can then configure all the options') ?>
        <?php } ?>
    </div>
</fieldset>