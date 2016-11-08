<?php
namespace Concrete\Package\AttributeForms\Attribute\Key;

use Concrete\Core\Attribute\Key\Key as AttributeKey,
    Concrete\Core\Attribute\Value\Value,
    Concrete\Package\AttributeForms\Attribute\Value\AttributeFormValue,
    Concrete\Core\Attribute\Value\ValueList as AttributeValueList,
    Database;

use Concrete\Core\Attribute\Type as AttributeType;

class AttributeFormKey extends AttributeKey
{

    protected $atObj;
    
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
        $values = $db->fetchAll("select akID, avID from AttributeFormsAttributeValues where afID = ?", array($afID));
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
        $av = AttributeFormValue::getByID($avID);
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

    /**
     *
     * @return AttributeFormKey[]
     */
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

        $qb = $db->createQueryBuilder();
        $subQb = $db->createQueryBuilder();
        
        $qb->delete('AttributeValues')->where(
            $qb->expr()->comparison('avID',  'IN', '(' . $subQb->select('atfv.avID')
                                        ->from('AttributeFormsAttributeValues', 'atfv')
                                        ->where($subQb->expr()->eq('atfv.akID', $this->getAttributeKeyID()))
                                        ->getSQL() . ')'
                )
        );
        $db->query($qb->getSQL());

        $db->delete('AttributeFormsAttributeValues', array('akID' => $this->getAttributeKeyID()));
    }

    /**
     * Returns an attribute type object.
     * @return AttributeType
     */
    public function getAttributeType()
    {
        if (!is_object($this->atObj)) {
            $this->atObj = AttributeType::getByID($this->atID);
        }
        return $this->atObj;
    }
    
    /**
     * Removes the attribute value from a particular attribute forms ID from following tables:
     * - AttributeValues
     * - AttributeFormsAttributeValues
     * 
     * @param int $afID     attribute forms ID
     */
    public function removeAttributeValue($afID)
    {
        $db = Database::connection();

        $qb = $db->createQueryBuilder();
        $subQb = $db->createQueryBuilder();
        
        $avID = $db->fetchColumn('Select avID from AttributeFormsAttributeValues where afID=? and akID=?', array($afID, $this->getAttributeKeyID()));
        $avObj = Value::getByID($avID);
        $avObj->delete();
        
        $qb->delete('AttributeValues')->where(
            $qb->expr()->comparison('avID',  'IN', '(' . $subQb->select('atfv.avID')
                                        ->from('AttributeFormsAttributeValues', 'atfv')
                                        ->where($subQb->expr()->eq('atfv.afID', $afID))
                                        ->andWhere($subQb->expr()->eq('atfv.akID', $this->getAttributeKeyID()))
                                        ->getSQL() . ')'
                )
        );
        $db->query($qb->getSQL());

        $db->delete('AttributeFormsAttributeValues', array('afID' => $afID, 'akID' => $this->getAttributeKeyID()));
    }

    /**
     * Renders a view for this attribute key. If no view is default we display it's "view"
     * Valid views are "view", "form" or a custom view (if the attribute has one in its directory)
     * Additionally, an attribute does not have to have its own interface. If it doesn't, then whatever
     * is printed in the corresponding $view function in the attribute's controller is printed out.
     */
    public function render($view = 'view', $value = false, $return = false)
    {
        $resp = $this->getAttributeType()->render($view, $this, $value, $return);
        if ($return) {
            return $resp;
        } else {
            print $resp;
        }
    }
}
