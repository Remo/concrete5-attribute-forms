<?php
namespace Concrete\Package\AttributeForms\ActionType\Mail;

use Concrete\Package\AttributeForms\Form\ActionType\AbstractController;
use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;
use Concrete\Package\AttributeForms\Entity\AttributeForm;
use Concrete\Package\AttributeForms\MeschApp;
use Config;

class Controller extends AbstractController
{
    public $mailTos;

    public function __construct()
    {
        parent::__construct();
        $this->mailTos = [
            'site_owner' => t('Site Owner'),
            'submitor' => t('Submitor')
        ];
    }

    public function getPackageHandle()
    {
        return MeschApp::pkgHandle();
    }

    public function validateForm(array $data, $actionID)
    {
        $value = $this->getValue();
        $value->setActionData($this->getParsedData($data, $actionID));
        
        $val = $this->app->make('helper/validation/form');
        $val->setData($value->getDataArray());
        $val->addRequired('mailTo', t('Please select mail destination'));
        $val->addRequired('mailSubject', t('Please fill Mail Subject'));
        $val->addRequired('mailBody', t('Please fill Mail Body'));

        if (!$val->test()) {
            return $val->getError();
        }

        return true;
    }
    
    public function execute(AttributeForm $form, array $data = array())
    {
        $mailTo = $this->fieldValue('mailTo');
        if ($mailTo == 'site_owner') {
            $this->sendNotificationMailToAdmin($form, $data);
        } elseif ($mailTo == 'submitor') {
            $this->sendNotificationsMailToSubmitor($form);
        }
    }

    private function sendNotificationMailToAdmin(AttributeForm $af, $data)
    {
        $afs = $this->app->make('mesch/atf/string', [$af]);
        $cfg         = MeschApp::getFileConfig();
        $fromAddress = $cfg->get("mesch.email.address", Config::get('concrete.email.form_block.address'));

        if (empty($fromAddress) || !strstr($fromAddress, '@')) {
            $fromAddress = $this->app->make('Concrete\Core\User\UserInfoFactory')->getByID(USER_SUPER_ID)->getUserEmail();
        }

        $aft   = $af->getTypeObj();
        $attrs = $aft->getAttributesByAttrType('email', 'send_notification_from');

        $mh = $this->app->make('helper/mail'); /* @var $mh \Concrete\Core\Mail\Service */
        $mh->to($data['recipientEmail']);
        $mh->from($fromAddress);

        foreach ($attrs as $akID => $attr) {
            $replyTo = $af->getAttribute(AttributeFormKey::getByID($akID), 'display');
            $mh->replyto($replyTo);
        }

        $mh->setSubject($afs->parse($this->fieldValue('mailSubject')));
        $mh->setBodyHTML($afs->parse($this->fieldValue('mailBody')));
        @$mh->sendMail();
    }

    private function sendNotificationsMailToSubmitor(AttributeForm $af)
    {
        $afs = $this->app->make('mesch/atf/string', [$af]);
        $cfg         = MeschApp::getFileConfig();
        $fromAddress = $cfg->get("mesch.email.address", Config::get('concrete.email.default.address'));
        $fromName    = $cfg->get("mesch.email.name", Config::get('concrete.email.default.name'));

        $aft   = $af->getTypeObj();
        $attrs = $aft->getAttributesByAttrType('email', 'send_notification_from');

        if (empty($attrs)) {
            return false;
        }

        $mh = $this->app->make('helper/mail'); /* @var $mh \Concrete\Core\Mail\Service */
        $mh->from($fromAddress, $fromName);

        foreach ($attrs as $akID => $attr) {
            $toEmailAddress = $af->getAttribute(AttributeFormKey::getByID($akID), 'display');
            $mh->to($toEmailAddress);
        }

        $mh->setSubject($afs->parse($this->fieldValue('mailSubject')));
        $mh->setBodyHTML($afs->parse($this->fieldValue('mailBody')));
        @$mh->sendMail();
    }

}