<?php
namespace Concrete\Package\AttributeForms\ActionType\Mail;

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
        $value = $this->getValue();
        $value->setActionData($this->getParsedData($data, $actionID));
        
        $val = Core::make('helper/validation/form');
        $val->setData($value->getDataArray());
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