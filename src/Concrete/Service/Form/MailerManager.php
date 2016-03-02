<?php
namespace Concrete\Package\AttributeForms\Service\Form;

class MailerManager extends ActionManager
{
    /**
     * @var null|self
     */
    private static $loc = null;

    private function __construct()
    {

    }

    /**
     * @return MailerManager
     */
    public static function getInstance()
    {
        if (null === self::$loc) {
            self::$loc = new self;
        }
        return self::$loc;
    }
}