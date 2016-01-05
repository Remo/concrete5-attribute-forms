<?php
defined('C5_EXECUTE') or die("Access Denied.");
/* @var $af Concrete\Package\AttributeForms\Entity\AttributeForm */
$aft           = $af->getTypeObj();
$submittedData = '';
foreach ($af->getAttributeObjects() as $ak) {
    $submittedData .= $ak->getAttributeKeyDisplayName()."\r\n";
    $submittedData .= $af->getAttribute($ak, 'display')."\r\n"."\r\n";
}

$body = $aft->getFormName()."\r\n"."\r\n".$submittedData."\r\n".Config::get('concrete.site')."\r\n".BASE_URL;