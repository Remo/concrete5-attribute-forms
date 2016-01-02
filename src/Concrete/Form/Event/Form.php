<?php

namespace Concrete\Package\AttributeForms\Form\Event;

use \Symfony\Component\EventDispatcher\Event as AbstractEvent;
use Concrete\Package\AttributeForms\Block\AttributeForm\Controller as AttributeFormBlock;
use Concrete\Package\AttributeForms\Entity\AttributeForm;

class Form extends AbstractEvent
{
    protected $atFormBlock;
    protected $atForm;

    public function __construct(AttributeFormBlock $atFormBlock, AttributeForm $atForm = null)
    {
        $this->atFormBlock = $atFormBlock;
        $this->atForm      = $atForm;
    }

    /**
     *
     * @return AttributeFormBlock
     */
    public function getAttributeFormBlock()
    {
        return $this->atFormBlock;
    }

    /**
     *
     * @return AttributeForm
     */
    public function getAttributeForm()
    {
        return $this->atForm;
    }
}