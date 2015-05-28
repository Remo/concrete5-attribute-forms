<?php
namespace Concrete\Package\AttributeForms\Block\AttributeForm;

use Concrete\Package\AttributeForms\Models\AttributeFormType;
use Concrete\Package\AttributeForms\Models\AttributeFormTypeList,
    Concrete\Core\Block\BlockController;


class Controller extends BlockController
{
    protected $btTable = 'btAttributeForm';
    protected $btInterfaceWidth = "500";
    protected $btInterfaceHeight = "365";
    protected $helpers = ['form'];

    public function getBlockTypeName()
    {
        return t('Attribute Form');
    }

    public function getBlockTypeDescription()
    {
        return t('Inserts a form based on pre-defined types');
    }

    public function add()
    {
        $formTypes = AttributeFormTypeList::getList();
        $this->set('formTypes', $formTypes);
    }

    public function edit()
    {
        $this->add();
    }

    public function view() {
        $formType = AttributeFormType::getByID($this->aftID);
        $attributes = $formType->getAttributeObjects();
        $this->set('attributes', $attributes);
    }

}