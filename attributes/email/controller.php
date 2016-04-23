<?php

namespace Concrete\Package\AttributeForms\Attribute\Email;

use Concrete\Core\Attribute\DefaultController;
use Core;
use Database;

class Controller extends DefaultController
{
    protected $searchIndexFieldDefinition = array('type' => 'text', 'options' => array('length' => 4294967295, 'default' => null, 'notnull' => false));

    public $helpers = array('form');

    public function form()
    {
        $this->load();
        if (is_object($this->attributeValue)) {
            $value = Core::make('helper/text')->email($this->getAttributeValue()->getValue());
        }
        print Core::make('helper/form')->email($this->field('value'), $value, array( 'placeholder' => $this->akEmailPlaceholder));
    }

    public function composer()
    {
        if (is_object($this->attributeValue)) {
            $value = Core::make('helper/text')->email($this->getAttributeValue()->getValue());
        }
        print Core::make('helper/form')->email($this->field('value'), $value, array('class' => 'col-xs-5'));
    }

    public function validateForm($data)
    {
        $valid = false;
        if($data['value'] != ''){
            $val = Core::make('helper/validation/strings');
            if (!$val->email($data['value'])) {
                $e = Core::make('helper/validation/error');
                $e->add(t('Invalid email address provided.'));
                $valid = $e;
            }else{
                $valid = true;
            }
        }
        return $valid;
    }

    public function saveKey($data)
    {
        $data += array(
            'akEmailPlaceholder' => null,
        );
        $akEmailPlaceholder = $data['akEmailPlaceholder'];

        $this->setDisplayMode($akEmailPlaceholder);
    }


    public function setDisplayMode($akEmailPlaceholder)
    {
        $db = Database::connection();
        $ak = $this->getAttributeKey();

        $db->Replace('atEmailSettings', array(
            'akID' => $ak->getAttributeKeyID(),
            'akEmailPlaceholder' => $akEmailPlaceholder
        ), array('akID'), true);
    }

    // should have to delete the at thing
    public function deleteKey()
    {
        $db = Database::connection();
        $arr = $this->attributeKey->getAttributeValueIDList();
        foreach ($arr as $id) {
            $db->Execute('delete from atDefault where avID = ?', array($id));
        }

        $db->Execute('delete from atEmailSettings where akID = ?', array($this->attributeKey->getAttributeKeyID()));
    }

    public function type_form()
    {
        $this->load();
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $db = Database::connection();
        $row = $db->GetRow('select akEmailPlaceholder from atEmailSettings where akID = ?', array($ak->getAttributeKeyID()));
        $this->akEmailPlaceholder = $row['akEmailPlaceholder'];

        $this->set('akEmailPlaceholder', $this->akEmailPlaceholder);
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('mode', $this->akEmailPlaceholder);

        return $akey;
    }

    public function importKey($akey)
    {
        if (isset($akey->type)) {
            $data['akEmailPlaceholder'] = $akey->type['mode'];
            $this->saveKey($data);
        }
    }

    public function duplicateKey($newAK)
    {
        $this->load();
        $db = Database::connection();
        $db->Replace('atEmailSettings', array(
            'akID' => $newAK->getAttributeKeyID(),
            'akEmailPlaceholder' => $this->akEmailPlaceholder,
        ), array('akID'), true);
    }
}