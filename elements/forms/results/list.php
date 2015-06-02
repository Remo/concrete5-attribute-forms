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
            <?php foreach ($formTypes as $formType) { ?>
                <tr>
                    <td><?= $formType->getFormName() ?></td>
                    <td>
                        <div class="btn-group pull-right">
                            <a class="btn btn-default"
                               href="<?php echo $view->action('excel', $formType->getID()) ?>"><?php echo t('Download Excel') ?>
                            </a>
                            <a class="btn btn-primary"
                               href="<?php echo $view->action('entries', $formType->getID()) ?>"><?php echo t('Show Entries') ?>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="8">
                    <?= $formTypesPagination ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
