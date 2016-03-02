<?php

namespace Concrete\Package\AttributeForms\Service\Form;

use Concrete\Package\AttributeForms\Entity\AttributeForm;

class MailerManager extends AbstractActionManager
{
    /**
     * @var null|self
     */
    protected static $loc = null;

    /**
     * Run saved action
     * @param string $name
     * @param AttributeForm $form
     * @param boolean $notifyAdminOnSubmission
     * @param boolean $notifySubmitor
     * @param string $adminEmail
     * @return mixed
     */
    public static function runAction($name, AttributeForm $form, $notifyAdminOnSubmission, $notifySubmitor, $adminEmail)
    {
        return parent::run($name, array($form, $notifyAdminOnSubmission, $notifySubmitor, $adminEmail));
    }
}