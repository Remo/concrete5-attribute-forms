<?php

namespace Concrete\Package\AttributeForms\Attribute\AttributeSwitcher;

use Concrete\Core\Support\Facade\Database,
    Loader,
    Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController
{

    protected $searchIndexFieldDefinition = array('type' => 'smallint', 'options' => array('notnull' => false));

    public function searchForm($list)
    {
        $val = $this->request('value');
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), is_null($val) ? 0 : $val);
        return $list;
    }

    public function getValue()
    {
        $db = Database::connection();
        $value = $db->GetOne("select value from atAttributeSwitcher where avID = ?", array($this->getAttributeValueID()));
        return $value;
    }

    public function exportKey($akey)
    {
        $this->load();
        $type = $akey->addChild('type');
        $type->addAttribute('checked', $this->akCheckedByDefault);
        return $akey;
    }

    public function importKey($akey)
    {
        if (isset($akey->type)) {
            $data['akCheckedByDefault'] = $akey->type['checked'];
            $this->saveKey($data);
        }
    }

    public function getDisplayValue()
    {
        $v = $this->getValue();
        return ($v == 1) ? t('Yes') : t('No');
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $db = Database::connection();
        $row = $db->GetRow('select akCheckedByDefault, checkedActions, uncheckedActions, indentation from atAttributeSwitcherSettings where akID = ?', $ak->getAttributeKeyID());
        $this->akCheckedByDefault = $row['akCheckedByDefault'];
        $this->akCheckedActions = $row['checkedActions'];
        $this->akUncheckedActions = $row['uncheckedActions'];
        $this->indentation = $row['indentation'];
        $this->set('indentation', $this->indentation);
        $this->set('akCheckedByDefault', $this->akCheckedByDefault);
        $this->set('akCheckedActions', json_decode($this->akCheckedActions, true));
        $this->set('akUncheckedActions', json_decode($this->akUncheckedActions, true));
    }

    public function form()
    {
        $this->load();

        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
            $checked = $value == 1 ? true : false;
        } else {
            if ($this->akCheckedByDefault) {
                $checked = true;
            }
        }

        $this->set('checked', $checked);
    }

    public function composer()
    {
        print '<label class="checkbox">';
        $this->form();
        print '</label>';
    }

    public function search()
    {
        print '<label class="checkbox">' . Loader::helper('form')->checkbox($this->field('value'), 1, $this->request('value') == 1) . ' ' . t('Yes') . '</label>';
    }

    public function type_form()
    {
        $this->set('form', Loader::helper('form'));
        $this->load();
    }

    // run when we call setAttribute(), instead of saving through the UI
    public function saveValue($value)
    {
        $db = Database::connection();
        $value = ($value == false || $value == '0') ? 0 : 1;
        $db->Replace('atAttributeSwitcher', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
    }

    public function deleteKey()
    {
        $db = Database::connection();
        $db->Execute('delete from atAttributeSwitcherSettings where akID = ?', array($this->getAttributeKey()->getAttributeKeyID()));

        $arr = $this->attributeKey->getAttributeValueIDList();
        foreach ($arr as $id) {
            $db->Execute('delete from atAttributeSwitcher where avID = ?', array($id));
        }
    }

    public function duplicateKey($newAK)
    {
        $this->load();
        $db = Database::connection();
        $db->Execute('insert into atAttributeSwitcherSettings (akID, akCheckedByDefault) values (?, ?)', array($newAK->getAttributeKeyID(), $this->akCheckedByDefault));
    }

    public function saveKey($data)
    {
        $ak = $this->getAttributeKey();
        $db = Database::connection();
        $akCheckedByDefault = $data['akCheckedByDefault'];

        if ($data['akCheckedByDefault'] != 1) {
            $akCheckedByDefault = 0;
        }

        $indentation = $data['indentation'] ?: 0;

        $checkedActions = json_encode($data['checkedActions']);
        $uncheckedActions = json_encode($data['uncheckedActions']);

        $db->Replace('atAttributeSwitcherSettings', array(
            'akID' => $ak->getAttributeKeyID(),
            'akCheckedByDefault' => $akCheckedByDefault,
            'checkedActions' => $checkedActions,
            'uncheckedActions' => $uncheckedActions,
            'indentation' => $indentation,
        ), array('akID'), true);
    }

    public function saveForm($data)
    {
        $this->saveValue($data['value']);
    }

    // if this gets run we assume we need it to be validated/checked
    public function validateForm($data)
    {
        return $data['value'] == 1;
    }

    public function deleteValue()
    {
        $db = Database::connection();
        $db->Execute('delete from atAttributeSwitcher where avID = ?', array($this->getAttributeValueID()));
    }

}