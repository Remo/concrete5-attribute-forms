<?php
namespace Concrete\Package\AttributeForms\Src\Entity;

use Concrete\Core\Support\Facade\Database,
    Concrete\Core\Attribute\Key\Key as AttributeKey,
    Concrete\Package\AttributeForms\Src\Attribute\Key\AttributeFormKey,
    Concrete\Package\AttributeForms\Src\Attribute\Value\AttributeFormValue;

use Concrete\Package\AttributeForms\Src\AttributeFormList;
use Concrete\Package\AttributeForms\Src\Entity\AttributeFormType;

/**
 * @Entity
 * @Table(name="AttributeForms",indexes={@Index(name="AttributeForms_IX1", columns={"aftID"})})
 * @HasLifecycleCallbacks
 */
class AttributeForm
{
    /** 
     * @Id @Column(name="afID",type="integer",options={"unsigned"=true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $ID;

    /**
     * @Column(name="aftID", type="integer")
     */
    protected $typeID;

    /**
     * @Column(type="integer",options={"unsigned"=true,"default"=0})
     */
    protected $isSpam;

    /**
     * @Column(type="datetime")
     */
    protected $dateCreated;

    /**
     * @Column(type="datetime")
     */
    protected $dateUpdated;


    private $autoIndex = true;

    
    public function __construct()
    {
        $this->isSpam =  0;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function getTypeID()
    {
        return $this->typeID;
    }

    public function getIsSpam()
    {
        return $this->isSpam;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }
    
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    function setID($ID)
    {
        $this->ID = $ID;
    }

    function setTypeID($typeID)
    {
        $this->typeID = $typeID;
    }

    public function setIsSpam($isSpam)
    {
        $this->isSpam = $isSpam;
    }

    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }
    
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    public function getTypeName()
    {
        $aft = AttributeFormType::getByID($this->getTypeID());
        return $aft->getFormName();
    }

    public function isSpam()
    {
        return $this->isSpam == 1;
    }

    /**
     *
     * @param int $ID
     * @return AttributeForm
     */
    public static function getByID($ID)
    {
        $attrFormLst = new AttributeFormList();
        return $attrFormLst->getByID($ID);
    }

    public function disableAutoIndex()
    {
        $this->autoIndex = false;
    }

    public function enableAutoIndex()
    {
        $this->autoIndex = true;
    }

    public function markAsSpam()
    {
        return $this->isSpam = 1;
    }

   /**
    * @PrePersist
    */
    public function prePersist()
    {
        $currentDate = new \DateTime();
        $this->setDateCreated($currentDate);
        $this->setDateUpdated($currentDate);
    }

    /**
     * @PreUpdate
     */
    public function preUpdate()
    {
        $currentDate = new \DateTime();
        $this->setDateUpdated($currentDate);
    }

    /**
     * @PostRemove
     */
    public function postRemove()
    {
        $db = Database::connection();
        $id = $this->getID();

        $r = $db->Execute('select avID, akID from AttributeFormsAttributeValues where afID = ?', array($id));

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

    public function setAttribute(AttributeFormKey $ak, $value)
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
