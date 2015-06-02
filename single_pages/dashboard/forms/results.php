<?php
defined('C5_EXECUTE') or die('Access Denied.');

$taskElements = array(
    'view' => 'forms/results/list',
    'entries' => 'forms/results/form_list',
    'detail' => 'forms/results/detail',
);

$element = $taskElements[$this->controller->getTask()];
Loader::element($element, get_defined_vars() + ['view' => $this], 'attribute_forms');
