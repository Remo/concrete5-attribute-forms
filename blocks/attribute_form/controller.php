<?php

namespace Concrete\Package\AttributeForms\Block\AttributeForm;

use Concrete\Core\Block\BlockController;
use Concrete\Package\AttributeForms\Controller\BlockControllerExtension;
use Concrete\Package\AttributeForms\AttributeFormTypeList;
use Concrete\Package\AttributeForms\Entity\AttributeFormType;
use Concrete\Package\AttributeForms\Entity\AttributeForm;
use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;
use Concrete\Package\AttributeForms\Form\Event\Form as AttributeFormEvent;
use Concrete\Package\AttributeForms\Form\ActionType\Factory as ActionTypeFactory;
use Concrete\Core\User\UserInfo,
    Database,
    Request,
    Events,
    Config,
    Core;

class Controller extends BlockController
{
    protected $btTable           = 'btAttributeForm';
    protected $btInterfaceWidth  = "500";
    protected $btInterfaceHeight = "365";
    protected $helpers           = ['form'];
    protected $btCacheBlockRecord = false;

    use BlockControllerExtension;

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
        $this->setApplication(Core::getFacadeApplication());
        $this->bControllerExtensionInit();
    }

    public function on_before_render()
    {
        parent::on_before_render();
        $this->prepareSessionSets();
    }
    
    public function add()
    {
        $attFormTypeLst = new AttributeFormTypeList();
        $attFormTypeLst->sortByFormName();

        $this->set('formTypes', $attFormTypeLst->getArray());
        $this->set('actionTypes', ActionTypeFactory::get());
        $this->requireAsset('css', 'mesch/attribute_form/backend');
        $this->requireAsset('redactor');
    }

    public function edit()
    {
        $this->add();
        $this->set('customActions', $this->getCustomFormActions());
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('javascript','bootstrap/alert');
    }
    
    public function view()
    {
        $formType = AttributeFormType::getByID($this->aftID);
        if (!is_object($formType)) {
            return;
        }

        $token = $this->app->make('token');
        if (starts_with($this->getCurrentTemplateName(), 'step_by_step')) {
            $formPage = $formType->getFirstFormPage();
            $this->set('formPage', $formPage);
            $this->set('nextFormPage', $formType->getNextFormPage($formPage->handle));
            $this->set('token', $token->generate('attribute_form_'.$this->bID.'_'.$formPage->handle));
        } else {
            $this->set('attributes', $formType->getDecodedAttributes());
            $this->set('layoutAttributes', $formType->getLayoutDecodedAttributes());
            $this->set('token', $token->generate('attribute_form_'.$this->bID));
        }

        $this->set('aftID', $this->aftID);
        $this->set('captcha', $this->displayCaptcha ? $formType->getCaptchaLibrary() : false);
    }

    public function customLayout()
    {
        $formType = AttributeFormType::getByID($this->aftID);

        if (!is_object($formType)) {
            return;
        }

        $token = $this->app->make('token');
        $this->set('aftID', $this->aftID);
        $this->set('attributes', $formType->getDecodedAttributes());
        $this->set('captcha', $this->displayCaptcha ? $formType->getCaptchaLibrary() : false);
        $this->set('token', $token->generate('attribute_form_'.$this->bID));
    }

    public function duplicate($newBID)
    {
        parent::duplicate($newBID);
        $db = Database::connection();
        $qb = $db->createQueryBuilder()->insert('btAttributeFormAction')->values(array(
            'bID' => ':bID', 'actionName' => ':actionName',
            'actionType' => ':actionType', 'actionData' => ':actionData'
        ));

        $r = $this->getCustomFormActions();
        foreach ($r as $row) {
            $qbc = clone $qb;
            $qbc->setParameters(array(
                'bID' => $newBID,
                'actionName' => $row['actionName'],
                'actionType' => $row['actionType'],
                'actionData' => $row['actionData'],
                'executionOrder' => $row['executionOrder']
            ))->execute();
        }
    }
    
    public function save($data)
    {
        if (!isset($data['submitText'])) {
            $data['submitText'] = '';
        }

        if (!isset($data['notifyMeOnSubmission'])) {
            $data['notifyMeOnSubmission'] = 0;
        }

        if (!isset($data['thankyouMsg'])) {
            $data['thankyouMsg'] = '';
        }

        if (!isset($data['displayCaptcha'])) {
            $data['displayCaptcha'] = 0;
        }

        // Clean up Extra Spaces between emails
        if(!empty($data['recipientEmail'])){
            $emails = array_map('trim', explode(',',$data['recipientEmail']));
            $data['recipientEmail'] = implode(',', $emails);
        }

        parent::save($data);

        $db = Database::connection();
        $db->delete('btAttributeFormAction', ['bID' => $this->bID]);
        if(is_array($data['customActions'])){
            for ($i = 0; $i < count($data['customActions']); $i++) {
                $at = ActionTypeFactory::getByHandle($data['actionType'][$i]);
                $actionData = $at->getParsedData($data, $data['actionID'][$i]);
                $db->insert('btAttributeFormAction',[
                    'bID' => $this->bID,
                    'actionName' => $data['actionName'][$i],
                    'actionType' => $data['actionType'][$i],
                    'actionData' => $actionData,
                    'executionOrder' => $i
                ]);
            }
        }
    }

    public function delete()
    {
        $qb = Database::connection()->createQueryBuilder();
        $qb->delete('btAttributeFormAction')->where($qb->expr()->eq('bID', ':bID'))
            ->setParameter('bID', $this->bID)->execute();

        parent::delete();
    }

    public function validate($args)
    {
        if (is_array($args['customActions'])) {
            for ($i = 0; $i < count($args['customActions']); $i++) {
                $actionName = trim($args['actionName'][$i]);
                if (empty($actionName)) {
                    $this->errors->add(t('The Action Name cannot be empty'));
                }
                $actionType = ActionTypeFactory::getByHandle($args['actionType'][$i]);
                $e = $actionType->validateForm($args, $args['actionID'][$i]);
                if ($e instanceof \Concrete\Core\Error\Error) {
                    $this->errors->add($e);
                }
            }
        }
        return $this->errors;
    }

    public function action_step($step, $bID)
    {
        if ($this->bID != $bID) {
            return false;
        }

        $aft = AttributeFormType::getByID($this->aftID);
        $this->securityCheck($aft, $step);

        if($this->errors->has()){
            return $this->goBackToCurrentStep();
        }
        
        $this->session->set('attrFormCurrentStep', $step);
        $this->set('nextFormPage', $aft->getNextFormPage($step));
        $this->set('prevFormPage', $aft->getPrevFormPage($step));
        $this->set('formPage', $aft->getFormPage($step));
        $this->set('token', $this->app->make('token')->generate('attribute_form_'.$this->bID.'_'.$step));
        $this->set('aftID', $this->aftID);
        $this->set('captcha', $this->displayCaptcha ? $aft->getCaptchaLibrary() : false);
        $akIDs = @unserialize($this->session->get('attrForm'));
        $this->set('requestArray', is_array($akIDs) ? ['akID' => $akIDs] : false);
    }

    public function action_step_submit($nextFormPageHandle, $bID)
    {
        if ($this->bID != $bID) {
            return false;
        }

        // Check if is back to previous request
        if($this->post('previousBtn')){
            $this->session->set('attrFormCurrentStep', $nextFormPageHandle);
            return $this->redirect($this->urlToAction('step', $nextFormPageHandle));
        }
        
        Events::dispatch('pre_attribute_forms_submit', new AttributeFormEvent($this));
        $aft = AttributeFormType::getByID($this->aftID);
        
        $formPageHandle = $this->securityCheck($aft, $nextFormPageHandle);
        if ($nextFormPageHandle == 'complete') {
            $activeFormPage = $aft->getFormPage($this->post('formPageHandle'));
        } else {
            $activeFormPage = $aft->getFormPage($nextFormPageHandle);
        }

        if ($this->errors->has()) {
            $this->goBackToCurrentStep();
            return;
        }
        
        $formPage = $aft->getFormPage($formPageHandle);
        if (!$formPage) {
            $this->errors->add(t('Invalid step requested'));
            $this->goBackToCurrentStep();
            return;
        }
        
        foreach ($formPage->attributes as $attr) {
            if ($attr->required) {
                $ak = AttributeFormKey::getByID($attr->akID);
                $e1 = $ak->validateAttributeForm();
                if ($e1 == false) {
                    $this->errors->add(t('The field "%s" is required', $ak->getAttributeKeyDisplayName()));
                } else if ($e1 instanceof \Concrete\Core\Error\Error) {
                    $this->errors->add($e1);
                }
            }
        }

        if ($this->errors->has()) {
            $this->goBackToCurrentStep();
            return;
        }

        $aks    = $this->post('akID');
        $afVals = $this->session->get('attrForm');
        if ($afVals) {
            $aks = unserialize($afVals) + $aks;
        }

        $attributeFileValues = $this->session->get('attrFiles');
        $files = $_FILES;
        array_walk_recursive($files , [$this, 'replaceTempFile']);
        if ($attributeFileValues) {
            $fileValues = unserialize($attributeFileValues) + $files;
        }
        else {
            $fileValues = $files;
        }

        if ($nextFormPageHandle != 'complete') {
            $this->session->set('attrFormCurrentStep', $nextFormPageHandle);
            $this->session->set('attrForm', serialize($aks));
            $this->session->set('attrFiles', serialize($fileValues));
        } else {
            $_POST['akID'] = $aks;
            $_FILES = $fileValues;
            $this->saveAttributeForm($aft);
            $this->session->remove('attrFormCurrentStep');
            $this->session->remove('attrForm');
            $this->session->remove('attrFiles');
            $this->redirectToView();
        }

        $this->redirect($this->urlToAction('step', $nextFormPageHandle));
    }

    private function replaceTempFile(&$value, $key)
    {
        $fh = Core::make('helper/file');
        $tempDir = $fh->getTemporaryDirectory();

        if ($key == 'value') {
            if (is_uploaded_file($value)) {
                $tempFile = tempnam($tempDir, 'files');
                if (move_uploaded_file($value, $tempFile)); {
                    $value = $tempFile;
                }
            }
        }
    }

    private function securityCheck($aft, $nextFormPageHandle)
    {
        $ip = $this->app->make('helper/validation/ip');
        if ($ip->isBanned()) {
            $this->errors->add($ip->getErrorMessage());
            return;
        }

        if (Request::isPost()) {
            if ($this->displayCaptcha && !$this->post('previousBtn')) {
                if (!$aft->getCaptchaLibrary()->check()) {
                    $this->errors->add(t("Incorrect captcha code"));
                    $_REQUEST['ccmCaptchaCode'] = '';
                    return;
                }
            }

            $formPageHandle = $this->post('formPageHandle');
            if ($formPageHandle) {
                // check CSRF token
                $token = $this->app->make('token');
                if (!$token->validate('attribute_form_'.$this->bID.'_'.$formPageHandle, $this->post('af_token'))) {
                    $this->errors->add($token->getErrorMessage());
                }
            }
        } else {
            // If page reloaded without post request
            $formPageHandle = $this->session->get('attrFormCurrentStep');
            if (!$formPageHandle || $formPageHandle != $nextFormPageHandle) {
                $this->errors->add(t('Invalid step requested'));
            }
        }

        return $formPageHandle;
    }

    private function goBackToCurrentStep()
    {
        $formPageHandle = $this->post('formPageHandle');
        $this->flashError('errors', $this->errors);

        if (!$formPageHandle) {
            $this->session->remove('attrFormCurrentStep');
            $this->redirectToView();
        } else {
            $this->session->set('attrFormCurrentStep', $formPageHandle);
            $this->redirect($this->urlToAction('step', $formPageHandle));
        }
    }

    public function action_submit($bID = false)
    {
        if ($this->bID != $bID) {
            return false;
        }

        $this->view();

        Events::dispatch('pre_attribute_forms_submit', new AttributeFormEvent($this));

        $ip = $this->app->make('helper/validation/ip');
        if ($ip->isBanned()) {
            $this->errors->add($ip->getErrorMessage());
            return;
        }

        // check CSRF token
        $token = $this->app->make('token');
        if (!$token->validate('attribute_form_'.$this->bID, $this->post('_token'))) {
            throw new \Exception('Invalid token');
        }

        // get objects
        $aftID = $this->post('aftID');
        $aft   = AttributeFormType::getByID($aftID);

        if ($this->displayCaptcha) {
            $captcha = $aft->getCaptchaLibrary();
            if (!$captcha->check()) {
                $this->errors->add(t("Incorrect captcha code"));
                $_REQUEST['ccmCaptchaCode'] = '';
            }
        }
        
        $attributes = $aft->getDecodedAttributes();
        if($attributes) {
            foreach ($attributes->formPages as $formPage) {
                foreach ($formPage->attributes as $attr) {
                    if ($attr->required) {
                        $ak = AttributeFormKey::getByID($attr->akID);
                        $e1 = $ak->validateAttributeForm();
                        if ($e1 == false) {
                            $this->errors->add(t('The field "%s" is required', $ak->getAttributeKeyDisplayName()));
                        } else if ($e1 instanceof \Concrete\Core\Error\Error) {
                            $this->errors->add($e1);
                        }
                    }
                }
            }
        }

        if($this->errors->has()){
            return;
        }
        
        $this->saveAttributeForm($aft);
        $this->redirectToView();
    }

    protected function saveAttributeForm(AttributeFormType $aft)
    {
        // create new form entry
        $af = new AttributeForm();
        $af->setTypeID($this->aftID);
        $af->save();

        // get all attributes of type and save values from form to the database
        $attributeObjs = $aft->getAttributeObjects();
        foreach ($attributeObjs as $akID => $ak) {
            $af->setAttribute($ak, false);
        }

        // check SPAM
        $submittedData = $af->getAttributeDataString();
        $antispam      = $this->app->make('helper/validation/antispam');
        $foundSpam     = !$antispam->check($submittedData, 'attribute_form');

        if ($foundSpam) {
            if ($aft->getDeleteSpam()) {
                $af->delete();
            } else {
                $af->markAsSpam();
                $af->save();
            }
        }

        if (!$foundSpam) {

            foreach ($this->getCustomFormActions() as $customAction) {
                ActionTypeFactory::execute(
                    $customAction, [$af, ['recipientEmail' => $this->recipientEmail]]
                );
            }

            if (intval($this->notifyMeOnSubmission) > 0) {
                $this->sendNotificationMailToAdmin($af);
            }

            if (intval($this->notifySubmitor) > 0) {
                $this->sendNotificationsMailToSubmitor($af);
            }
            Events::dispatch('post_attribute_forms_submit', new AttributeFormEvent($this, $af));
            $this->flash('message', h(t($this->thankyouMsg)));
        }

        $this->redirectToView();
    }

    public function action_layoutSubmit($bID = false)
    {
        if ($this->bID != $bID) {
            return false;
        }

        $this->view();

        Events::dispatch('pre_attribute_forms_submit', new AttributeFormEvent($this));

        $ip = $this->app->make('helper/validation/ip');
        if ($ip->isBanned()) {
            $this->errors->add($ip->getErrorMessage());
            return;
        }

        // check CSRF token
        $token = $this->app->make('token');
        if (!$token->validate('attribute_form_'.$this->bID, $this->post('_token'))) {
            throw new \Exception('Invalid token');
        }

        // get objects
        $aftID = $this->post('aftID');
        $aft   = AttributeFormType::getByID($aftID);

        if ($this->displayCaptcha) {
            $captcha = $aft->getCaptchaLibrary();
            if (!$captcha->check()) {
                $this->errors->add(t("Incorrect captcha code"));
                $_REQUEST['ccmCaptchaCode'] = '';
            }
        }

        $attributes = $aft->getLayoutDecodedAttributes();

        foreach ($attributes->formPages as $row => $formPageRow){
            if(is_array($formPageRow)) {
                foreach ($formPageRow as $col => $formPageCol) {
                    if (is_object($formPageCol)) {
                        foreach ((array)$formPageCol as $key => $formPage) {
                            if (is_array($formPage)) {
                                foreach ($formPage as $attr) {
                                    if ($attr->required) {
                                        $ak = AttributeFormKey::getByID($attr->akID);
                                        $e1 = $ak->validateAttributeForm();

                                        if ($e1 == false) {
                                            $this->errors->add(t('The field "%s" is required', $ak->getAttributeKeyDisplayName()));
                                        } else if ($e1 instanceof \Concrete\Core\Error\Error) {
                                            $this->errors->add($e1);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if($this->errors->has()){
            return;
        }

        // create new form entry
        $af = new AttributeForm();
        $af->setTypeID($aftID);
        $af->save();

        // get all attributes of type and save values from form to the database
        $attributeObjs = $aft->getLayoutAttributeObjects();

        foreach ($attributeObjs as $akID => $ak) {
            if($akID) {
                $af->setAttribute($ak, false);
            }
        }

        // check SPAM
        $submittedData = $af->getLayoutAttributeDataString();
        $antispam      = $this->app->make('helper/validation/antispam');
        $foundSpam     = !$antispam->check($submittedData, 'attribute_form');

        if ($foundSpam) {

            if ($aft->getDeleteSpam()) {
                $af->delete();
            } else {
                $af->markAsSpam();
                $af->save();
            }
        }

        if (!$foundSpam) {
            foreach ($this->getCustomFormActions() as $customAction) {
                ActionTypeFactory::execute(
                    $customAction, [$af, ['recipientEmail' => $this->recipientEmail]]
                );
            }

            if (intval($this->notifyMeOnSubmission) > 0) {
                $this->sendNotificationMailToAdmin($af);
            }

            if (intval($this->notifySubmitor) > 0) {
                $this->sendNotificationsMailToSubmitor($af);
            }

            Events::dispatch('post_attribute_forms_submit', new AttributeFormEvent($this, $af));
            $this->flash('message', h(t($this->thankyouMsg)));
        }

        $this->redirectToView();
    }
    
    private function sendNotificationMailToAdmin(AttributeForm $af)
    {
        $fromAddress = Config::get("concrete.email.attribute_form.address");
        $fromName    = Config::get("concrete.email.attribute_form.name");

        if (empty($fromAddress) || !strstr($fromAddress, '@')) {
            $fromAddress = UserInfo::getByID(USER_SUPER_ID)->getUserEmail();
        }

        $aft   = $af->getTypeObj();
        $attrs = $aft->getAttributesByAttrType('email', 'send_notification_from');

        $mh = Core::make('helper/mail'); /* @var $mh \Concrete\Core\Mail\Service */
        $mh->to($this->recipientEmail);
        $mh->from($fromAddress, $fromName);

        foreach ($attrs as $akID => $attr) {
            $replyTo = $af->getAttribute(AttributeFormKey::getByID($akID), 'display');
            $mh->replyto($replyTo);
        }

        $mh->addParameter('af', $af);
        $mh->load('attribute_form_admin_notif', 'attribute_forms');
        $mh->setSubject(t('%s Attribute Form Submission', $aft->getFormName()));
        @$mh->sendMail();
    }

    private function sendNotificationsMailToSubmitor(AttributeForm $af)
    {
        $fromAddress = Config::get("concrete.email.attribute_form.address");
        $fromName    = Config::get("concrete.email.attribute_form.name");

        if (empty($fromAddress) || !strstr($fromAddress, '@')) {
            $fromAddress = UserInfo::getByID(USER_SUPER_ID)->getUserEmail();
        }
        
        $aft   = $af->getTypeObj();
        $attrs = $aft->getAttributesByAttrType('email', 'send_notification_from');

        if (empty($attrs)) {
            return false;
        }
        
        $mh = Core::make('helper/mail'); /* @var $mh \Concrete\Core\Mail\Service */
        $mh->from($fromAddress, $fromName);
        $mh->replyto($fromAddress, $fromName);

        foreach ($attrs as $akID => $attr) {
            $toEmailAddress = $af->getAttribute(AttributeFormKey::getByID($akID), 'display');
            $mh->to($toEmailAddress);
        }

        $mh->addParameter('af', $af);
        $mh->load('attribute_form_submitor_notif', 'attribute_forms');

        $subject = t('%s Attribute Form Submission', $aft->getFormName());
        $attrs   = $aft->getAttributesByAttrType('text', 'message_subject');
        
        if (!empty($attrs)) {
            $subject = Config::get("concrete.site").": ";
            foreach ($attrs as $akID => $attr) {
                $subject .= $af->getAttribute(AttributeFormKey::getByID($akID), 'display');
            }
        }

        $mh->setSubject($subject);
        @$mh->sendMail();
    }

    /**
     * Get list of defined form actions
     * @return array
     */
    public function getCustomFormActions()
    {
        $db = Database::connection();
        $qb = $db->createQueryBuilder()->select('*')->from('btAttributeFormAction');
        $qb->where($qb->expr()->eq('bID', ':bID'))->setParameter('bID', $this->bID)
           ->orderBy('executionOrder');

        return $qb->execute()->fetchAll();
    }
    
    /**
     * Use this method to display captcha image since the core tools
     * uses the Default Active Captcha that is set in C5 Settings
     * @param \Concrete\Core\Captcha\Controller $captcha
     * @param int $aftID
     */
    public function diaplayCaptcha($captcha, $aftID)
    {
        $ci   = $this->app->make('helper/concrete/urls');
        $pictureDispURL = $ci->getToolsURL('captcha');

        $meschPictureDispURL = \URL::to('/ccm/attribute_forms/tools/captcha/', $aftID);

        ob_start();
        $captcha->display();
        $captchaDisplay = ob_get_clean();
        
        print str_replace($pictureDispURL, $meschPictureDispURL, $captchaDisplay);
    }
}
