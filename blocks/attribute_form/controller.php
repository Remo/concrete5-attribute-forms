<?php
namespace Concrete\Package\AttributeForms\Block\AttributeForm;

use Concrete\Core\Validation\CSRF\Token,
    Concrete\Package\AttributeForms\Models\AttributeFormType,
    Concrete\Package\AttributeForms\Models\AttributeFormTypeList,
    Concrete\Core\Block\BlockController;
use Concrete\Package\AttributeForms\Models\AttributeForm;

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

        $token = new Token();

        $this->set('attributes', $attributes);
        $this->set('aftID', $this->aftID);
        $this->set('token', $token->generate('attribute_form_'. $this->bID));
    }

    public function action_submit()
    {
        // check CSRF token
        $token = new Token();
        if(!$token->validate('attribute_form_'. $this->bID, $this->post('_token'))) {
            throw new \Exception('Invalid token');
        }

        $aftID = $this->post('aftID');

        $aft = AttributeFormType::getByID($aftID);
        $af = AttributeForm::add(['aftID' => $aftID]);

        $attributes = $aft->getAttributeObjects();

        foreach ($attributes as $akID => $ak)
        {
            $af->setAttribute($ak, false);
        }
    }

}