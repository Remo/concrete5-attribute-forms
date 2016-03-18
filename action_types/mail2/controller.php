<?php
namespace Concrete\Package\AttributeForms\ActionType\Mail2;

use Concrete\Package\AttributeForms\Form\ActionType\AbstractController;
use Concrete\Package\AttributeForms\Entity\AttributeForm;
use Concrete\Package\AttributeForms\MeschApp;
use Core;

class Controller extends AbstractController
{

    public function getPackageHandle()
    {
        return MeschApp::pkgHandle();
    }

    public function validateForm(array $data, $actionID)
    {
        if(!empty($data)){
            $this->getValue()->setActionData($actionData);
        }
        
        $val = Core::make('helper/validation/form');
        $val->setData($this->getValue()->getDataArray());
        $val->addRequired('mailSubject', t('Please fill Mail Subject'));
        $val->addRequired('mailBody', t('Please fill Mail Body'));

        if (!$val->test()) {
            return $val->getError();
        }

        return true;
    }
    
    public function execute(AttributeForm $form, array $data = array())
    {
        
    }

}