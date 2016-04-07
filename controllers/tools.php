<?php

namespace Concrete\Package\AttributeForms\Controller;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Package\AttributeForms\Entity\AttributeFormType;
use Concrete\Package\AttributeForms\Form\ActionType\Factory as ActionTypeFactory;
use Concrete\Package\AttributeForms\Form\ActionType\Value as ActionTypeValue;
use Controller;

class Tools extends Controller
{

    public function displayCaptchaPicture($atFormTypeID)
    {
        $atFormType = AttributeFormType::getByID($atFormTypeID);
        $captcha    = $atFormType->getCaptchaLibrary();
        $captcha->displayCaptchaPicture();
        die();
    }

    public function formActionTypeAction($handle, $action)
    {
        $formAcType = ActionTypeFactory::getByHandle($handle);
        
        $args = $this->request('args', array());
        if (!is_array($args)) {
            $args = array();
        }

        if(method_exists($formAcType, 'action_' . $action)) {
            call_user_func_array(array($formAcType, 'action_' . $action), $args);
        }
    }

    public function renderFormActionType($handle, $view)
    {
        $value = json_decode($this->request('value'), true);
        ActionTypeFactory::render($handle, $view, new ActionTypeValue($value));
    }
}