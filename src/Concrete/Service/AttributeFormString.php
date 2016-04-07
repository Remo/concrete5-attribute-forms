<?php
namespace Concrete\Package\AttributeForms\Service;

use Concrete\Package\AttributeForms\Entity\AttributeForm;

class AttributeFormString
{
    /** @var AttributeForm */
    private $af;

    public function __construct(AttributeForm $af)
    {
        $this->af = $af;
    }

    protected function replaceAttribute($matches)
    {
        if (count($matches) == 2) {
            return $this->af->getAttribute($matches[1], 'display');
        }
    }

    public function parse($input)
    {
        return preg_replace_callback('/{attr:([a-zA-Z0-9\_\-]*)}/ism', array($this, 'replaceAttribute'), $input);
    }
}