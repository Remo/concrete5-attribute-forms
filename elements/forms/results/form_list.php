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
            <?php
            if(count($forms) > 0):
                foreach ($forms as $form): ?>                
                <tr>
                    <td><?= $form->getID() ?></td>
                    <td><?= $date->formatDateTime($form->getDateCreated()) ?></td>
                    <?php if ($showSpam) { ?>
                        <td><?= $form->getIsSpam() ? t('Yes') : t('No') ?></td>
                    <?php } ?>
                    <td>
                        <a class="btn btn-danger pull-right"
                           href="<?= $view->action('delete', $form->getID()) ?>"><?= t('Delete') ?>
                        </a>
                        <a class="btn btn-primary pull-right" style="margin-right: 5px;"
                           href="<?= $view->action('detail', $form->getID()) ?>"><?= t('Show') ?>
                        </a>
                    </td>
                </tr>
            <?php
                endforeach;
            else: ?>
                <tr>
                    <td colspan="4">
                        <h4><?=t('No entries found.');?></h4>
                    </td>
                </tr>
            <?php
            endif; ?>
            </tbody>
            <?php if (isset($formsPagination)): ?>
                <tfoot>
                    <tr>
                        <td colspan="4">
                            <?= $formsPagination ?>
                        </td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
