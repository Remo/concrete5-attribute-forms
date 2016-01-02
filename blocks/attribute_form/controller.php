<?php

namespace Concrete\Package\AttributeForms\Block\AttributeForm;

use Concrete\Core\Validation\CSRF\Token;
use Concrete\Package\AttributeForms\Entity\AttributeFormType;
use Concrete\Package\AttributeForms\AttributeFormTypeList;
use Concrete\Package\AttributeForms\Entity\AttributeForm;
use Concrete\Package\AttributeForms\Form\Event\Form as AttributeFormEvent;
use Concrete\Core\Block\BlockController;
use Events,
    Config,
    Core;
    

class Controller extends BlockController
{
    protected $btTable           = 'btAttributeForm';
    protected $btInterfaceWidth  = "500";
    protected $btInterfaceHeight = "365";
    protected $helpers           = ['form'];

    /** @var \Concrete\Core\Error\Error */
    private $errors;
    private $success_msg = array();

    public function getBlockTypeName()
    {
        return t('Attribute Form');
    }

    public function getBlockTypeDescription()
    {
        return t('Inserts a form based on pre-defined types');
    }

    public function __construct($obj = null)
    {
        parent::__construct($obj);
        $this->errors      = new \Concrete\Core\Error\Error();
    }

    public function on_before_render()
    {
        parent::on_before_render();

        $session = Core::make('session');
        if ($session->getFlashBag()->has('custom_message')) {
            $value = $session->getFlashBag()->get('custom_message');
            foreach ($value as $message) {
                $this->set($message[0], $message[1]);
            }
        } else {
            $this->set('success_msg', $this->success_msg);
        }

        if ($session->getFlashBag()->has('custom_error')) {
            $value = $session->getFlashBag()->get('custom_error');
            foreach ($value as $message) {
                $this->set($message[0], $message[1]);
            }
        } else {
            $this->set('errors', $this->errors);
        }
    }
    
    public function add()
    {
        $attFormTypeLst = new AttributeFormTypeList();
        $attFormTypeLst->sortByFormName();
        $formTypes      = $attFormTypeLst->getArray();
        $this->set('formTypes', $formTypes);
    }

    public function edit()
    {
        $this->add();
    }

    public function view()
    {
        $formType   = AttributeFormType::getByID($this->aftID);
        $attributes = $formType->getDecodedAttributes();

        $token = new Token();

        $this->set('attributes', $attributes);
        $this->set('aftID', $this->aftID);
        $this->set('token', $token->generate('attribute_form_'.$this->bID));
    }

    public function action_submit()
    {
        Events::dispatch('pre_attribute_forms_submit', new AttributeFormEvent($this));
        // check CSRF token
        $token = new Token();
        if (!$token->validate('attribute_form_'.$this->bID, $this->post('_token'))) {
            throw new \Exception('Invalid token');
        }

        // get objects
        $aftID = $this->post('aftID');
        $aft   = AttributeFormType::getByID($aftID);

        // create new form entry
        $af    = new AttributeForm();
        $af->setTypeID($aftID);
        $af->save();

        // get all attributes of type and save values from form to the database
        $attributes = $aft->getAttributeObjects();
        foreach ($attributes as $akID => $ak) {
            $af->setAttribute($ak, false);
        }

        // check SPAM
        $submittedData = $af->getAttributeDataString();
        $antispam      = Core::make('helper/validation/antispam');
        if (!$antispam->check($submittedData, 'attribute_form')) {
            if ($aft->getDeleteSpam()) {
                $af->delete();
            } else {
                $af->markAsSpam();
                $af->save();
            }
        }
        
        Events::dispatch('post_attribute_forms_submit', new AttributeFormEvent($this, $af));

        $this->redirectToView();
    }

    private function sendNotificationsMail(AttributeFormType $aft, AttributeForm $af){
        if (intval($this->notifyMeOnSubmission) > 0 ) {
            if (Config::get('concrete.email.form_block.address') && strstr(Config::get('concrete.email.form_block.address'), '@')) {
                $formFormEmailAddress = Config::get('concrete.email.form_block.address');
            } else {
                $adminUserInfo = UserInfo::getByID(USER_SUPER_ID);
                $formFormEmailAddress = $adminUserInfo->getUserEmail();
            }

            /* @var $mh \Concrete\Core\Mail\Service */
            $mh = Core::make('helper/mail');
            $mh->to($this->recipientEmail);
            $mh->from($formFormEmailAddress);
            $mh->replyto($replyToEmailAddress);
            $mh->addParameter('formName', $this->surveyName);
            $mh->addParameter('questionSetId', $this->questionSetId);
            $mh->addParameter('questionAnswerPairs', $questionAnswerPairs);
            $mh->load('block_form_submission', 'attribute_forms');
            $mh->setSubject(t('%s Form Submission', $aft->getFormName()));
            //echo $mh->body.'<br>';
            @$mh->sendMail();
        }

        if (intval($this->notifySubmitor) > 0 ) {
            if (Config::get('concrete.email.form_block.address') && strstr(Config::get('concrete.email.form_block.address'), '@')) {
                $formFormEmailAddress = Config::get('concrete.email.form_block.address');
            } else {
                $adminUserInfo = UserInfo::getByID(USER_SUPER_ID);
                $formFormEmailAddress = $adminUserInfo->getUserEmail();
            }

            /* @var $mh \Concrete\Core\Mail\Service */
            $mh = Core::make('helper/mail');
            $mh->to($this->recipientEmail);
            $mh->from($formFormEmailAddress);
            $mh->replyto($replyToEmailAddress);
            $mh->addParameter('formName', $this->surveyName);
            $mh->addParameter('questionSetId', $this->questionSetId);
            $mh->addParameter('questionAnswerPairs', $questionAnswerPairs);
            $mh->load('block_form_submission', 'attribute_forms');
            $mh->setSubject(t('%s Form Submission', $aft->getFormName()));
            //echo $mh->body.'<br>';
            @$mh->sendMail();
        }
    }

    private function redirectToView($message = false, $errors = false)
    {
        $session = Core::make('session');

        if ($message) {
            $session->getFlashBag()->add('custom_message',
                array('success_msg', $message));
        }

        if ($errors) {
            $session->getFlashBag()->add('custom_error',
                array('errors', $errors));
        }

        $arguments = array($this->getCollectionObject());
        $url       = call_user_func_array(array('\URL', 'to'), $arguments);
        $this->redirect($url.'?'.http_build_query($_GET));
    }
}