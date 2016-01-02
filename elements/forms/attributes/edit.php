<?php
defined('C5_EXECUTE') or die('Access Denied.');

if (isset($type)) {
    ?>
    <form method="post" action="<?= $view->action('update') ?>" id="ccm-attribute-key-form">
        <?php View::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>
    </form>
<?php
}