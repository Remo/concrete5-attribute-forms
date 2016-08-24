<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div class="row">
    <div class="col-xs-12">
        <?php if(isset($errors) && $errors->has()):?>
            <div class="alert alert-danger">
                <a data-dismiss="alert" href="#" class="close"><span class="text-danger">&times;</span></a>
                <?=t('Please correct the following errors:')?>
                <?php $errors->output(); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($warningMsg) && !empty($warningMsg)): ?>
            <div class="alert alert-warning">
                <a data-dismiss="alert" href="#" class="close"><span class="text-warning">&times;</span></a>
                <p><?= $warningMsg; ?></p>
            </div>
        <?php endif; ?>
        <?php
        if(!empty($message)): ?>
            <div class="alert alert-success">
                <a data-dismiss="alert" href="#" class="close"><span class="text-success">&times;</span></a>
                <?php if (is_array($message)): ?>
                    <ul>
                        <?php foreach ($message as $msg): ?>
                            <li><?= $msg; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <?= $message; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>