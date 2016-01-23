<?php

namespace Concrete\Package\AttributeForms\Controller;

defined('C5_EXECUTE') or die("Access Denied.");

use Controller;
use Concrete\Package\AttributeForms\Entity\AttributeFormType;

class Tools extends Controller
{

    public function displayCaptchaPicture($atFormTypeID)
    {
        $atFormType = AttributeFormType::getByID($atFormTypeID);
        $captcha    = $atFormType->getCaptchaLibrary();
        $captcha->displayCaptchaPicture();
        die();
    }
}