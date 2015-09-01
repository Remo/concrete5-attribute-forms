<?php
defined('C5_EXECUTE') or die('Access Denied.');

$taskElements = array(
    'view' => 'forms/types/list',
    'add' => 'forms/types/form',
    'edit' => 'forms/types/form',
);

$element = $taskElements[$this->controller->getTask()];
View::element($element, get_defined_vars() + ['view' => $this], 'attribute_forms');
