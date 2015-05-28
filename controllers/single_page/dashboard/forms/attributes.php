<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard\Forms;

use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Package\AttributeForms\Src\Attribute\Key\AttributeFormKey;
use Page;
use Loader;

class Attributes extends PageController
{
    protected $helpers = array('form');

    public function on_start()
    {
        $this->set('category', AttributeKeyCategory::getByHandle('attribute_form'));
    }

    public function view($message = false)
    {
        // set action message if set
        switch ($message) {
            case 'attribute_created':
                $this->set('message', t('Attribute created'));
                break;
            case 'attribute_updated':
                $this->set('message', t('Attribute updated'));
                break;
            case 'attribute_deleted':
                $this->set('message', t('Attribute deleted'));
                break;
        }

        $attribs = AttributeFormKey::getList();

        $this->set('attribs', $attribs);
        $this->set('types', $this->getAttributeTypes());
    }

    public function add()
    {
        $atID = $this->request('atID');
        $at = AttributeType::getByID($atID);
        $this->set('type', $at);
    }

    public function insert()
    {
        $this->add();
        $type = $this->get('type');
        $cnt = $type->getController();
        $e = $cnt->validateKey($this->post());
        if ($e->has()) {
            $this->set('error', $e);
        } else {
            $type = AttributeType::getByID($this->post('atID'));
            AttributeFormKey::add($type, $this->post());
            $this->redirect('/dashboard/forms/attributes/', 'attribute_created');
        }
    }

    public function edit($akID = 0)
    {
        $key = AttributeFormKey::getByID($akID);
        if (!is_object($key) || $key->isAttributeKeyInternal()) {
            $this->redirect('/dashboard/forms/attributes/');
        }
        $type = $key->getAttributeType();
        $this->set('key', $key);
        $this->set('type', $type);
    }

    public function update()
    {
        $akID = $this->post('akID');
        $key = AttributeFormKey::getByID($akID);
        if (!is_object($key) || $key->isAttributeKeyInternal()) {
            $this->redirect('/dashboard/forms/attributes/');
        }
        $type = $key->getAttributeType();

        $cnt = $type->getController();
        $cnt->setAttributeKey($key);
        $e = $cnt->validateKey($this->post());
        if ($e->has()) {
            $this->set('error', $e);
        } else {
            AttributeType::getByID($this->post('atID'));
            $key->update($this->post());
            $this->redirect('/dashboard/forms/attributes/', 'attribute_updated');
        }
    }

    public function delete($akID, $token = null){
        try {
            $ak = AttributeFormKey::getByID($akID);

            if(!($ak instanceof AttributeFormKey)) {
                throw new Exception(t('Invalid attribute ID.'));
            }

            $valt = Loader::helper('validation/token');
            if (!$valt->validate('delete_attribute', $token)) {
                throw new Exception($valt->getErrorMessage());
            }

            $ak->delete();

            $this->redirect("/dashboard/forms/attributes", 'attribute_deleted');
        } catch (Exception $e) {
            $this->set('error', $e);
        }
    }

    protected function getAttributeTypes()
    {
        $attributeFormTypes = AttributeType::getList('attribute_form');
        $types = array();
        foreach ($attributeFormTypes as $attributeFormType) {
            $types[$attributeFormType->getAttributeTypeID()] = $attributeFormType->getAttributeTypeDisplayName();
        }
        return $types;
    }
}