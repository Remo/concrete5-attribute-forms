<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <?= t('Form Result - %d', $afID) ?>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><?= t('ID') ?></th>
                <th><?= t('Value') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $attributes = array_merge($attributes, $layoutAttributes);?>

            <?php foreach ($attributes as $attribute) {
                if($attribute) { ?>
                    <tr>
                        <th>
                            <?= $attribute->getAttributeKeyDisplayName() ?>
                        </th>
                        <td>
                            <?= $af->getAttribute($attribute, 'display') ?>
                        </td>
                    </tr>
                    <?php
                }
            } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="btn-toolbar">
    <a href="<?=$view->action('entries', $aftID);?>" class="btn btn-default"> <?=t('Back');?> </a>
</div>