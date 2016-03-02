<?php

namespace Concrete\Package\AttributeForms\Service\Form;

use Concrete\Package\AttributeForms\Entity\AttributeForm;


class ActionManager extends AbstractActionManager
{
    /**
     * @var null|self
     */
    protected static $loc = null;
    
    /**
     * Run saved action
     * @param string $name
     * @param AttributeForm $form
     * @return mixed
     */
    public static function runAction($name, AttributeForm $form)
    {
        return parent::run($name, array($form));
    }
}