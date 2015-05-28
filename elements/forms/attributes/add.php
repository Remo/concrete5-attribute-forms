<?php
defined('C5_EXECUTE') or die('Access Denied.');

if (isset($type)) {
    ?>
    <form method="post" action="<?php echo $view->action('insert') ?>" id="ccm-attribute-key-form">
        <?php Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>
    </form>
<?php
}