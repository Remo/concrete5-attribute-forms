<?php
namespace Concrete\Package\AttributeForms\Entity;

use Concrete\Core\Attribute\Key\Key as AttributeKey;
use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;
use Concrete\Package\AttributeForms\Attribute\Value\AttributeFormValue;
use Concrete\Package\AttributeForms\HtmLawed;
use Database,
    DateTime;

/**
 * @Entity
 * @Table(name="AttributeForms",indexes={@Index(name="AttributeForms_IX1", columns={"aftID"})})
 * @HasLifecycleCallbacks
 *
 * @method AttributeForm getByID(mixed $id)
 */
class AttributeForm extends EntityBase
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

    /**
     * Return Attribute Form Type object
     * @return AttributeFormType
     */
    public function getTypeObj()
    {
        return AttributeFormType::getByID($this->getTypeID());
    }
    
    public function getTypeName()
    {
        return $this->getTypeObj()->getFormName();
    }

    public function isSpam()
    {
        return $this->isSpam == 1;
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
        $currentDate = new DateTime();
        $this->setDateUpdated($currentDate);
    }

    /**
     * @PostRemove
     */
    public function postRemove()
    {
        $db = Database::connection();
        $id = $this->getID();

        $r = $db->executeQuery('select avID, akID from AttributeFormsAttributeValues where afID = ?', array($id));

        while ($row = $r->FetchRow()) {
            $uak = AttributeFormKey::getByID($row['akID']);
            $av = $this->getAttributeValueObject($uak);
            if (is_object($av)) {
                $av->delete();
            }
        }

        $db->delete('AttributeFormsIndexAttributes', array('afID' => $this->getID()));
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

    public function getLayoutAttributeDataString()
    {
        $ret = '';
        $aft = AttributeFormType::getByID($this->getTypeID());
        $attributes = $aft->getLayoutAttributeObjects();
        foreach ($attributes as $attribute) {
            if($attribute) {
                $ret .= sprintf('%s: %s', $attribute->getAttributeKeyDisplayName(), $this->getAttribute($attribute, 'display'));
            }
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

        $db->delete('AttributeFormsIndexAttributes', array('afID' => $this->getID()));
        $searchableAttributes = array('afID' => $this->getID());
        $rs = $db->executeQuery('select * from AttributeFormsIndexAttributes where afID = -1');
        AttributeKey::reindex('AttributeFormsIndexAttributes', $searchableAttributes, $attribs, $rs);
    }

    public function getAttributeValueObject($ak, $createIfNotFound = false)
    {
        $db = Database::connection();
        $av = false;
        $v = array($this->getID(), $ak->getAttributeKeyID());

        $avID = $db->fetchColumn("select avID from AttributeFormsAttributeValues where afID = ? and akID = ?", $v);
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
                $cnt = $db->fetchColumn("select count(avID) from AttributeFormsAttributeValues where avID = ?",
                    array($av->getAttributeValueID()));
            }

            if ((!is_object($av)) || ($cnt > 1)) {
                $av = $ak->addAttributeValue();
            }
        }

        return $av;
    }

    public function getAsText()
    {
        $aft = $this->getTypeObj();

        $submittedData = '';
        foreach ($aft->getAttributeObjects() as $ak) {
            $label = $ak->getAttributeKeyDisplayName();
            $value = $this->getAttribute($ak, 'display');

            $submittedData .= h($label) . "\r\n";
            $submittedData .= h($value) ."\r\n"."\r\n";
        }

        return $submittedData;
    }

    public function getAsHtml()
    {
        $aft = $this->getTypeObj();
        $configHtmLawed = array('safe'=>1);
        $submittedDataHtml = '<table>';
        foreach ($aft->getAttributeObjects() as $ak) {
            $label = $ak->getAttributeKeyDisplayName();
            $value = $this->getAttribute($ak, 'display');
            if($ak->getAttributeTypeHandle() == 'email'){
                $value = str_replace('@', '<span>@</span>', $value);
            }
            $submittedDataHtml .= '<tr><th align="right">' . HtmLawed::htmLawed($label, $configHtmLawed) . '</th><td>' . HtmLawed::htmLawed($value, $configHtmLawed) . '</td></tr>';
        }
        $submittedDataHtml .= '</table>';

        return $submittedDataHtml;
    }



    public function getLayoutAsHtml()
    {
        $aft = $this->getTypeObj();
        $configHtmLawed = array('safe'=>1);
        $submittedDataHtml = '<table>';
        foreach ($aft->getLayoutAttributeObjects() as $ak) {
            $label = $ak->getAttributeKeyDisplayName();
            $value = $this->getAttribute($ak, 'display');

            $submittedDataHtml .= '<tr><th>' . HtmLawed::htmLawed($label, $configHtmLawed) . '</th><td>' . HtmLawed::htmLawed($value, $configHtmLawed) . '</td></tr>';
        }
        $submittedDataHtml .= '</table>';

        return $submittedDataHtml;
    }

    public function getLayoutAsText()
    {
        $aft = $this->getTypeObj();

        $submittedData = '';
        foreach ($aft->getLayoutAttributeObjects() as $ak) {
            $label = $ak->getAttributeKeyDisplayName();
            $value = $this->getAttribute($ak, 'display');

            $submittedData .= h($label) . "\r\n";
            $submittedData .= h($value) ."\r\n"."\r\n";
        }

        return $submittedData;
    }
}
