<?php
defined('C5_EXECUTE') or die("Access Denied.");
/* @var $af Concrete\Package\AttributeForms\Entity\AttributeForm */
$aft           = $af->getTypeObj();

$body = $aft->getFormName()."\r\n"."\r\n".$af->getAsText()."\r\n".Config::get('concrete.site')."\r\n".BASE_URL;
$bodyHTML = "<p>" . $aft->getFormName()."</p>".$af->getAsHtml()."<p>".Config::get('concrete.site')."</p>".BASE_URL;
