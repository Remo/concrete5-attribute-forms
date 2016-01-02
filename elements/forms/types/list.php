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
            <?php
            if(count($formTypes) > 0):
                foreach ($formTypes as $formType): ?>
                    <tr>
                        <td><?= $formType->getFormName() ?></td>
                        <td>
                            <a class="btn btn-primary"
                               href="<?= $view->action('edit', $formType->getID()) ?>"><?= t('Edit') ?>
                            </a>
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
