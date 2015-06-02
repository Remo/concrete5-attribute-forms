<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <?= t('Form Results - %s', $formName) ?>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><?= t('ID') ?></th>
                <th><?= t('Date Created') ?></th>
                <?php if ($showSpam) { ?>
                    <th><?= t('Is SPAM?')?></th>
                <?php } ?>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($forms as $form) { ?>
                <tr>
                    <td><?= $form->getID() ?></td>
                    <td><?= $date->formatDateTime($form->getDateCreated()) ?></td>
                    <?php if ($showSpam) { ?>
                        <td><?= $form->getIsSpam() ? t('Yes') : t('No') ?></td>
                    <?php } ?>
                    <td>
                        <a class="btn btn-primary pull-right"
                           href="<?php echo $view->action('detail', $form->getID()) ?>"><?php echo t('Show') ?>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">
                    <?= $formsPagination ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
