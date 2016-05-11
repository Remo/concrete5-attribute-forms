<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <?= t('Forms') ?>
        </div>
    </div>
    <div class="panel-body">
        <table id="mesch-ftypes-list" class="table table-bordered">
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
                        <td class="form-type-name"><?= $formType->getFormName() ?></td>
                        <td>
                            <div class="btn-toolbar pull-right">
                                <a class="btn btn-primary" href="<?= $view->action('layout', $formType->getID()) ?>">
                                    <?= t('Custom Layout') ?>
                                </a>
                                <a class="btn btn-primary" href="<?= $view->action('edit', $formType->getID()) ?>">
                                    <?= t('Edit') ?>
                                </a>
                                <a class="btn btn-danger delete-form-type" 
                                   href="<?= $view->action('delete', $formType->getID(), Core::make('token')->generate('delete_ft')) ?>">
                                    <?= t('Delete') ?>
                                </a>

                            </div>
                        </td>
                    </tr>
                <?php
                endforeach;
            else: ?>
                <tr>
                    <td colspan="2">
                        <h4><?=t('No forms found.');?></h4>
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

        <a href="<?= $view->action('add'); ?>" class="btn btn-primary">
            <?= t('Add Form') ?>
        </a>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        attributeFormsApp.initFormTypesList({
            deleteFType: {
                msg:    <?=json_encode(t('Are you sure you want to remove this Form Type?'));?>,
                ok:     <?=json_encode(t('Ok'));?>,
                cancel: <?=json_encode(t('Cancel'));?>
            }
        });
    });
</script>