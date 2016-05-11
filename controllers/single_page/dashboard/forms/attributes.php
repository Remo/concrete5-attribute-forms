<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard\Forms;

use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Page\Controller\DashboardPageController;
use Core;

class Attributes extends DashboardPageController
{
    protected $helpers = array('form');

    public function on_start()
    {
        $this->set('category', AttributeKeyCategory::getByHandle('attribute_form'));
    }

    public function view()
    {
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
            $this->flash('error', $e);
        } else {
            $type = AttributeType::getByID($this->post('atID'));
            AttributeFormKey::add($type, $this->post());
            $this->flash("message", t('Attribute created'));
            $this->redirect($this->action(''));
        }
    }

    public function edit($akID = 0)
    {
        $key = AttributeFormKey::getByID($akID);
        if (!is_object($key) || $key->isAttributeKeyInternal()) {
            $this->redirect($this->action(''));
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
            $this->redirect($this->action(''));
        }
        $type = $key->getAttributeType();

        $cnt = $type->getController();
        $cnt->setAttributeKey($key);
        $e = $cnt->validateKey($this->post());
        if ($e->has()) {
            $this->flash('error', $e);
            $this->set('key', $key);
            $this->set('type', $type);
        } else {
            AttributeType::getByID($this->post('atID'));
            $key->update($this->post());
            $this->flash("message", t('Attribute updated'));
            $this->redirect($this->action(''));
        }
    }

    public function delete($akID, $token = null){
        try {
            $ak = AttributeFormKey::getByID($akID);

            if(!($ak instanceof AttributeFormKey)) {
                throw new Exception(t('Invalid attribute ID.'));
            }

            $valt = Core::make('token');
            if (!$valt->validate('delete_attribute', $token)) {
                throw new Exception($valt->getErrorMessage());
            }

            $ak->delete();

            $this->flash("message", t('Attribute deleted'));
            $this->redirect($this->action(''));
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