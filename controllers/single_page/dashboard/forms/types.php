<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard\Forms;

use Concrete\Package\AttributeForms\Src\Entity\AttributeFormType;
use PageController;
use Page;
use User;
use Loader;
use Localization;
use GroupList;
use Group;
use Concrete\Package\AttributeForms\Src\AttributeFormTypeList;
use Concrete\Package\AttributeForms\Src\Attribute\Key\AttributeFormKey;
use Concrete\Package\AttributeForms\Src\Entity\AttributeForm;

class Types extends PageController
{
    protected $helpers = array('form');

    public function view($message = false)
    {
        // set action message if set
        switch ($message) {
            case 'added':
                $this->set('message', t('Form type added'));
                break;
            case 'updated':
                $this->set('message', t('Form type updated'));
                break;
            case 'removed':
                $this->set('message', t('Form type removed'));
                break;
        }

        $aftl = new AttributeFormTypeList();
        $this->set('formTypes', $aftl->getPage());
        $this->set('formTypesPagination', $aftl->getPagination()->renderDefaultView());

        $this->requireAsset('css', 'core/frontend/pagination');
    }

    protected function getAttributeKeys()
    {
        $list = AttributeFormKey::getList();
        $attributes = array();
        foreach ($list as $item) {
            $attributes[$item->getAttributeKeyHandle()] = $item;
        }
        return $attributes;
    }

    public function add()
    {
        $this->requireAsset('javascript', 'underscore');
        $this->requireAsset('redactor');
        $this->requireAsset('core/file-manager');

        $attributeKeys = $this->getAttributeKeys();

        $this->set('attributeKeys', $attributeKeys);
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

        $aftLst = new AttributeFormTypeList();
        $em = $aftLst->getEntityManager();

        if ($aftID > 0) {
            $attributeFormType = AttributeFormType::getByID($aftID);
        } else {
            $attributeFormType = new AttributeFormType();
        }
        $attributeFormType->setFormName($formName);
        $attributeFormType->setDeleteSpam($deleteSpam);
        $attributeFormType->setAttributes(json_encode($attributes));

        $em->persist($attributeFormType);
        $em->flush();
        
        $this->redirect('/dashboard/forms/types/updated');

    }
}
