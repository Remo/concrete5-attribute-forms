<?php
namespace Concrete\Package\AttributeForms\Src\Attribute\Key;

use Concrete\Core\Attribute\Key\Key,
    Concrete\Package\AttributeForms\Src\Attribute\Value\AttributeFormValue as AttributeFormAttributeValue,
    Concrete\Core\Attribute\Value\ValueList as AttributeValueList,
    Loader,
    Concrete\Core\Support\Facade\Database;

class AttributeFormKey extends Key
{

    public function getIndexedSearchTable()
    {
        return 'AttributeFormsIndexAttributes';
    }

    protected $searchIndexFieldDefinition = array(
        'columns' => array(
            array('name' => 'afID', 'type' => 'integer', 'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true))
        ),
        'primary' => array('afID')
    );

    /**
     * Returns an attribute value list of attributes and values (duh) which a *** version can store
     * against its object.
     * @return AttributeValueList
     */
    public static function getAttributes($afID, $method = 'getValue')
    {
        $db = Database::connection();
        $values = $db->GetAll("select akID, avID from AttributeFormsAttributeValues where afID = ?", array($afID));
        $avl = new AttributeValueList();
        foreach ($values as $val) {
            $ak = static::getByID($val['akID']);
            if (is_object($ak)) {
                $value = $ak->getAttributeValue($val['avID'], $method);
                $avl->addAttributeValue($ak, $value);
            }
        }
        return $avl;
    }


    public function getAttributeValue($avID, $method = 'getValue')
    {
        $av = AttributeFormAttributeValue::getByID($avID);
        if (is_object($av)) {
            $av->setAttributeKey($this);
            return $av->{$method}();
        }
    }

    public static function getByID($akID)
    {
        $ak = new self();
        $ak->load($akID);
        if ($ak->getAttributeKeyID() > 0) {
            return $ak;
        }
    }

    public static function getByHandle($akHandle)
    {
        $ak = new self();
        $ak->load($akHandle, 'akHandle');
        if ($ak->getAttributeKeyID() < 1) {
            $ak = -1;
        }

        return $ak;
    }

    public static function getList()
    {
        return parent::getList('attribute_form');
    }


    public static function getColumnHeaderList()
    {
        return parent::getList('attribute_form', array('akIsColumnHeader' => 1));
    }

    public static function getSearchableIndexedList()
    {
        return parent::getList('attribute_form', array('akIsSearchableIndexed' => 1));
    }

    public static function getSearchableList()
    {
        return parent::getList('attribute_form', array('akIsSearchable' => 1));
    }

    /**
     * @access private
     */
    public function get($akID)
    {
        return static::getByID($akID);
    }

    protected function saveAttribute($object, $value = false)
    {
        $av = $object->getAttributeValueObject($this, true);
        parent::saveAttribute($av, $value);
        $db = Database::connection();
        $db->Replace('AttributeFormsAttributeValues', array(
            'afID' => $object->getID(),
            'akID' => $this->getAttributeKeyID(),
            'avID' => $av->getAttributeValueID()
        ), array('afID', 'akID'));
        unset($av);
    }

    public static function add($at, $args, $pkg = false)
    {

        // legacy check
        $fargs = func_get_args();
        if (count($fargs) >= 5) {
            $at = $fargs[4];
            $pkg = false;
            $args = array('akHandle' => $fargs[0], 'akName' => $fargs[1], 'akIsSearchable' => $fargs[2]);
        }

        $ak = parent::add('attribute_form', $at, $args, $pkg);
        return $ak;
    }

    public function delete()
    {
        parent::delete();
        $db = Database::connection();
        $r = $db->Execute('select avID from AttributeFormsAttributeValues where akID = ?', array($this->getAttributeKeyID()));
        while ($row = $r->FetchRow()) {
            $db->Execute('delete from AttributeValues where avID = ?', array($row['avID']));
        }
        $db->Execute('delete from AttributeFormsAttributeValues where akID = ?', array($this->getAttributeKeyID()));
    }

}
