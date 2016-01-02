<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <?= t('Form Results') ?>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><?= t('Name') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($formTypes) > 0):
                foreach ($formTypes as $formType): ?>
                <tr>
                    <td><?= $formType->getFormName() ?></td>
                    <td>
                        <div class="btn-group pull-right">
                            <a class="btn btn-default"
                               href="<?= $view->action('excel', $formType->getID()) ?>"><?= t('Download Excel') ?>
                            </a>
                            <a class="btn btn-primary"
                               href="<?= $view->action('entries', $formType->getID()) ?>"><?= t('Show Entries') ?>
                            </a>
                        </div>
                    </td>
                </tr>
             <?php
                endforeach;
            else: ?>
                <tr>
                    <td colspan="2">
                        <h4><?=t('No forms available.');?></h4>
                    </td>
                </tr>
            <?php
            endif; ?>
            </tbody>
            <?php if (isset($formTypesPagination)): ?>
                <tfoot>
                    <tr>
                        <td colspan="8">
                            <?= $formTypesPagination ?>
                        </td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
