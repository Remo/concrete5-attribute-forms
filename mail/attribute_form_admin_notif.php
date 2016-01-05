<?php 
defined('C5_EXECUTE') or die("Access Denied.");
/* @var $af Concrete\Package\AttributeForms\Entity\AttributeForm */
$aft           = $af->getTypeObj();
$submittedData = '';
foreach ($aft->getAttributeObjects() as $ak) {
    $submittedData .= $ak->getAttributeKeyDisplayName()."\r\n";
    $submittedData .= $af->getAttribute($ak, 'display')."\r\n"."\r\n";
}
$formDisplayUrl = URL::to('dashboard/forms/results/entries', $aft->getID());

$body = t("
There has been a submission of the form %s through your concrete5 website.

%s

To view all of this form's submissions, visit %s 

", $aft->getFormName(), $submittedData, $formDisplayUrl);