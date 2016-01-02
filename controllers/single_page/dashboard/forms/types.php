<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard\Forms;

use Concrete\Package\AttributeForms\Attribute\Key\FormKey as AttributeFormKey;
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
            $attributes[$ak->getAttributeKeyHandle()] = $item;
        }
        return $attributes;
    }

    public function add()
    {
        $this->requireAsset('javascript', 'underscore');
        $this->requireAsset('redactor');
        $this->requireAsset('core/file-manager');

        $this->set('attributeKeys', $this->getAttributeKeys());
    }

    public function edit($aftID)
    {
        $this->add();
        $attributeForm = AttributeFormType::getByID($aftID);
        $this->set('selectedAttributes', $attributeForm->getDecodedAttributes());
        $this->set('attributeForm', $attributeForm);
    }

    public function save($aftID = 0)
    {
        $formName = $this->post('formName');
        $deleteSpam = $this->post('deleteSpam', 0);

        $attributes = json_decode($this->post('attributes'));
        unset($attributes->attributeKeys);

        if ($aftID > 0) {
            $attributeFormType = AttributeFormType::getByID($aftID);
        } else {
            $attributeFormType = new AttributeFormType();
        }
        
        $attributeFormType->setFormName($formName);
        $attributeFormType->setDeleteSpam($deleteSpam);
        $attributeFormType->setAttributes(json_encode($attributes));
        $attributeFormType->save();

        if ($aftID > 0) {
            $this->flash("message", t('Form type updated'));
        }else{
            $this->flash("message", t('Form type added'));
        }
        $this->redirect($this->action(''));
    }
}
