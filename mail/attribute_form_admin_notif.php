<?php 
defined('C5_EXECUTE') or die("Access Denied.");

/* @var $af Concrete\Package\AttributeForms\Entity\AttributeForm */
$aft           = $af->getTypeObj();

$formDisplayUrl = URL::to('dashboard/forms/results/entries', $aft->getID());

$body = t("
There has been a submission of the form %s through your concrete5 website.

%s

To view all of this form's submissions, visit %s

", $aft->getFormName(), $af->getAsText(), $formDisplayUrl);


$bodyHTML = t("
<p>There has been a submission of the form %s through your concrete5 website.</p>
%s
<p>To view all of this form's submissions, visit <a href=\"%s\">%s</a></p>

", $aft->getFormName(), $af->getAsHtml(), $formDisplayUrl, $formDisplayUrl);
