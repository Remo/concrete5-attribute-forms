<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div class="row">
    <div class="col-xs-12">
        <?php if($errors->has()):?>
            <div class="alert alert-danger">
                <a data-dismiss="alert" href="#" class="close"><span class="text-danger">&times;</span></a>
                <?php $errors->output(); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($warningMsg) && !empty($warningMsg)): ?>
            <div class="alert alert-warning">
                <a data-dismiss="alert" href="#" class="close"><span class="text-warning">&times;</span></a>
                <p><?= $warningMsg; ?></p>
            </div>
        <?php endif; ?>
        <?php if (isset($successMsg) && !empty($successMsg)): ?>
            <div class="alert alert-success">
                <a data-dismiss="alert" href="#" class="close"><span class="text-success">&times;</span></a>
                <p><?= $successMsg; ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>