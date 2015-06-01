<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard\Forms;

use Concrete\Package\AttributeForms\Models\AttributeFormType;
use PageController;
use Page;
use User;
use Loader;
use Localization;
use GroupList;
use Group;
use Concrete\Package\AttributeForms\Models\AttributeFormTypeList;
use Concrete\Package\AttributeForms\Src\Attribute\Key\AttributeFormKey;
use Concrete\Package\AttributeForms\Models\AttributeForm;

class Types extends PageController
{
    protected $helpers = array('form');

    public function view($message = false)
    {
        $currentPage = Page::getCurrentPage();

        // set action message if set
        switch ($message) {
            case 'added':
                $this->set('message', t('Product template added'));
                break;
            case 'updated':
                $this->set('message', t('Product template updated'));
                break;
            case 'removed':
                $this->set('message', t('Product template removed'));
                break;
        }

        $aftl = new AttributeFormTypeList();
        $this->set('formTypes', $aftl->getPage());
        $this->set('formTypesPagination', $aftl->displayPagingV2(Loader::helper('navigation')->getLinkToCollection($currentPage), true));
    }

    protected function getAttributes()
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
        $this->requireAsset('redactor');
        $this->requireAsset('core/file-manager');

        $attributes = $this->getAttributes();

        $this->set('attributes', $attributes);
    }

    public function edit($aftID)
    {
        $this->add();
        $attributeForm = AttributeFormType::getByID($aftID);
        $this->set('selectedAttributes', $attributeForm->getAttributes());
        $this->set('attributeForm', $attributeForm);
    }

    public function save($aftID = 0)
    {
        $formName = $this->post('formName');
        $deleteSpam = $this->post('deleteSpam', 0);
        $data = ['formName' => $formName, 'deleteSpam' => $deleteSpam];
        if ($aftID > 0) {
            $attributeFormType = AttributeFormType::getByID($aftID);
            $attributeFormType->update($data);
        } else {
            $attributeFormType = AttributeFormType::add($data);
            $aftID = $attributeFormType->getID();
        }

        // set attributes
        $attributeFormType->setAttributes($this->post('attributes'));

        $this->redirect('/dashboard/forms/types/updated');

    }
}