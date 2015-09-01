<?php
defined('C5_EXECUTE') or die('Access Denied.');

$task = $this->controller->getTask();

$taskElements = array(
'view' => 'forms/attributes/list',
'add' => 'forms/attributes/add',
'edit' => 'forms/attributes/edit',
);

$element = $taskElements[$this->controller->getTask()];
View::element($element, get_defined_vars(), 'attribute_forms');


