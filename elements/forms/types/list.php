<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <?= t('Forms') ?>
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
                        <a class="btn btn-primary"
                           href="<?php echo $view->action('edit', $formType->getID()) ?>"><?php echo t('Edit') ?>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="8">
                    <a href="<?= View::url('/dashboard/forms/types/add') ?>" class="btn btn-primary">
                        <?= t('Add Form') ?>
                    </a>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
