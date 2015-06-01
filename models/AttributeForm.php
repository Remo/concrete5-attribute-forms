<?php
namespace Concrete\Package\AttributeForms\Models;

use Concrete\Core\Foundation\Object,
    Concrete\Core\Support\Facade\Database,
    Loader,
    Concrete\Core\Attribute\Key\Key as AttributeKey,
    Concrete\Package\AttributeForms\Src\Attribute\Key\AttributeFormKey,
    Concrete\Package\AttributeForms\Src\Attribute\Value\AttributeFormValue;

class AttributeForm extends Object
{
    public static $table = 'AttributeForms';
    private $autoIndex = true;

    public function __construct($afID = null, $row = null)
    {
        $this->afID = $afID;
        if ($row == null && $afID != null) {
            $db = Database::connection();
            $row = $db->GetRow('SELECT * FROM AttributeForms WHERE afID = ?', array($afID));
        }
        if ($row) {
            $this->setPropertiesFromArray($row);
        }
    }

    public function getID()
    {
        return $this->afID;
    }

    public function getTypeID()
    {
        return $this->aftID;
    }

    public function getTypeName()
    {
        $aft = AttributeFormType::getByID($this->getTypeID());
        return $aft->getFormName();
    }

    protected function load($ID)
    {
        $db = Database::connection();
        $row = $db->GetRow("SELECT * FROM " . self::$table . " WHERE afID = ?", array($ID));
        $this->setPropertiesFromArray($row);
    }

    public static function getByID($ID)
    {
        $ed = new self();
        $ed->load($ID);
        return $ed;
    }

    public static function add($data = [])
    {
        $db = Database::connection();
        $data['dateCreated'] = date('Y-m-d H:i:s');
        $data['dateUpdated'] = $data['dateCreated'];

        if ($db->insert(self::$table, $data)) {
            $ed = self::getByID($db->Insert_ID());
            return $ed;
        }
    }

    public function disableAutoIndex()
    {
        $this->autoIndex = false;
    }

    public function enableAutoIndex()
    {
        $this->autoIndex = true;
    }

    public function update($data = [])
    {
        $db = Database::connection();
        $data['dateUpdated'] = date('Y-m-d H:i:s');

        return $db->update(self::$table, $data, ['afID' => $this->getID()]);
    }

    public function markAsSpam()
    {
        return $this->update(['isSpam' => 1]);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->Execute("delete from " . self::$table . " where afID = ?", array($this->getID()));

        $r = $db->Execute('select avID, akID from AttributeFormsAttributeValues where afID = ?',
            array($this->getID()));

        while ($row = $r->FetchRow()) {
            $uak = AttributeFormKey::getByID($row['akID']);
            $av = $this->getAttributeValueObject($uak);
            if (is_object($av)) {
                $av->delete();
            }
        }

        $db->Execute('delete from AttributeFormsIndexAttributes where afID = ?', array($this->getID()));
    }

    public function getAttributeDataString()
    {
        $ret = '';
        $aft = AttributeFormType::getByID($this->getTypeID());
        $attributes = $aft->getAttributeObjects();
        foreach ($attributes as $attribute) {
            $ret .= sprintf('%s: %s', $attribute->getAttributeKeyDisplayName(), $this->getAttribute($attribute, 'display'));
        }
        return $ret;
    }

    public function getAttribute($ak, $displayMode = false)
    {
        if (!is_object($ak)) {
            $ak = AttributeFormKey::getByHandle($ak);
        }

        if (is_object($ak)) {
            $av = $this->getAttributeValueObject($ak);
            if (is_object($av)) {
                $args = func_get_args();
                if (count($args) > 1) {
                    array_shift($args);
                    return call_user_func_array(array($av, 'getValue'), $args);
                } else {
                    return $av->getValue($displayMode);
                }
            }
        }
    }

    public function setAttribute($ak, $value)
    {
        if (!is_object($ak)) {
            $ak = AttributeFormKey::getByHandle($ak);
        }

        $ak->setAttribute($this, $value);

        // make sure we don't reindex after each attribute
        if ($this->autoIndex) {
            $this->reindex();
        }
    }

    public function reindex()
    {
        $attribs = AttributeFormKey::getAttributes($this->getID(), 'getSearchIndexValue');

        $db = Database::connection();

        $db->Execute('delete from AttributeFormsIndexAttributes where afID = ?', array($this->getID()));
        $searchableAttributes = array('afID' => $this->getID());
        $rs = $db->Execute('select * from AttributeFormsIndexAttributes where afID = -1');
        AttributeKey::reindex('AttributeFormsIndexAttributes', $searchableAttributes, $attribs, $rs);
    }

    public function getAttributeValueObject($ak, $createIfNotFound = false)
    {
        $db = Database::connection();
        $av = false;
        $v = array($this->getID(), $ak->getAttributeKeyID());
        $avID = $db->GetOne("select avID from AttributeFormsAttributeValues where afID = ? and akID = ?", $v);
        if ($avID > 0) {
            $av = AttributeFormValue::getByID($avID);
            if (is_object($av)) {
                $av->setAttributeForm($this);
                $av->setAttributeKey($ak);
            }
        }

        if ($createIfNotFound) {
            $cnt = 0;

            // Is this avID in use ?
            if (is_object($av)) {
                $cnt = $db->GetOne("select count(avID) from AttributeFormsAttributeValues where avID = ?",
                    $av->getAttributeValueID());
            }

            if ((!is_object($av)) || ($cnt > 1)) {
                $av = $ak->addAttributeValue();
            }
        }

        return $av;
    }

}