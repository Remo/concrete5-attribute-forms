<?php

namespace Concrete\Package\AttributeForms\Block\AttributeForm;

use Concrete\Core\Validation\CSRF\Token,
    Concrete\Package\AttributeForms\Src\Entity\AttributeFormType,
    Concrete\Package\AttributeForms\Src\AttributeFormTypeList,
    Concrete\Package\AttributeForms\Src\AttributeFormList,
    Concrete\Core\Block\BlockController,
    Core,
    Concrete\Package\AttributeForms\Src\Entity\AttributeForm;

class Controller extends BlockController
{
    protected $btTable           = 'btAttributeForm';
    protected $btInterfaceWidth  = "500";
    protected $btInterfaceHeight = "365";
    protected $helpers           = ['form'];

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
        \Events::dispatch('pre_attribute_forms_submit', $this);
        // check CSRF token
        $token = new Token();
        if (!$token->validate('attribute_form_'.$this->bID,
                $this->post('_token'))) {
            throw new \Exception('Invalid token');
        }

        // get objects
        $aftID = $this->post('aftID');
        $aft   = AttributeFormType::getByID($aftID);

        $afLst = new AttributeFormList();
        $em    = $afLst->getEntityManager();
        // create new form entry
        $af    = new AttributeForm();
        $af->setTypeID($aftID);
        $em->persist($af);
        $em->flush();

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
                $em->remove($af);
            } else {
                $af->markAsSpam();
                $em->persist($af);
            }
        }
        $em->flush();

        \Events::dispatch('post_attribute_forms_submit', $this, $af);
    }
}