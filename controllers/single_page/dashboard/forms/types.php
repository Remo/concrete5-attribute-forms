<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard\Forms;

use Concrete\Package\AttributeForms\Attribute\Options as AttributeOptions;
use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;
use Concrete\Package\AttributeForms\AttributeFormTypeList;
use Concrete\Package\AttributeForms\Entity\AttributeFormType;
use Concrete\Core\Page\Controller\DashboardPageController;

class Types extends DashboardPageController
{
    protected $helpers = array('form');

    public function view()
    {
        $aftl = new AttributeFormTypeList();
        $this->set('formTypes', $aftl->getPage());

        $pagination = $aftl->getPagination();
        if ($pagination->haveToPaginate()) {
            $this->set('formTypesPagination', $pagination->renderDefaultView());
            $this->requireAsset('css', 'core/frontend/pagination');
        }
    }

    protected function getAttributeKeys()
    {
        $list = AttributeFormKey::getList();
        $attributes = array();
        foreach ($list as $ak) {
            $item = new \stdClass();
            $item->akID = $ak->getAttributeKeyID();
            $item->akName = $ak->getAttributeKeyDisplayName();
            $item->atHandle = $ak->getAttributeTypeHandle();
            $attributes[$ak->getAttributeKeyHandle()] = $item;
        }
        return $attributes;
    }

    public function add()
    {
        $this->requireAsset('javascript', 'underscore');
        $this->requireAsset('redactor');
        $this->requireAsset('core/file-manager');
        $this->requireAsset('javascript', 'mesch/attribute_form');

        $this->set('attributeOptions', AttributeOptions::get());
        $this->set('attributeKeys', $this->getAttributeKeys());
    }

    public function edit($aftID)
    {
        $this->add();
        $attributeForm = AttributeFormType::getByID($aftID);

        $selectedAttributes = $attributeForm->getDecodedAttributes(
                $includeAtHandle = true /* needed to determine attribute options */
        );
        
        $this->set('selectedAttributes', $selectedAttributes);
        $this->set('attributeForm', $attributeForm);
    }

    public function save($aftID = 0)
    {
        $formName = $this->post('formName');
        $deleteSpam = $this->post('deleteSpam', 0);

        if ($aftID > 0) {
            $attributeFormType = AttributeFormType::getByID($aftID);
        } else {
            $attributeFormType = new AttributeFormType();
        }
        
        $attributeFormType->setFormName($formName);
        $attributeFormType->setDeleteSpam($deleteSpam);
        $attributeFormType->setAttributes($this->post('attributes'));
        $attributeFormType->save();

        if ($aftID > 0) {
            $this->flash("message", t('Form type updated'));
        }else{
            $this->flash("message", t('Form type added'));
        }
        $this->redirect($this->action(''));
    }
}
