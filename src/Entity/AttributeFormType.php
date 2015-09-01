<?php
namespace Concrete\Package\AttributeForms\Src\Entity;

use Concrete\Package\AttributeForms\Src\AttributeFormTypeList,
    Concrete\Core\Attribute\Key\Key as AttributeKey;
use DateTime;


/**
 * @Entity
 * @Table(name="AttributeFormTypes")
 * @HasLifecycleCallbacks
 */
class AttributeFormType
{

    /**
     * @Id @Column(name="aftID",type="integer",options={"unsigned"=true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $ID;

    /**
     * @Column(type="string")
     */
    protected $formName;

    /**
     * @Column(type="text",  nullable=true)
     */
    protected $attributes;

    /**
     * @Column(type="boolean")
     */
    protected $deleteSpam;

    /**
     * @Column(type="datetime")
     */
    protected $dateCreated;

    /**
     * @Column(type="datetime")
     */
    protected $dateUpdated;

    

    /**
     *
     * @param int $ID
     * @return AttributeFormType
     */
    public static function getByID($ID)
    {
        $attrFormLst = new AttributeFormTypeList();
        return $attrFormLst->getByID($ID);
    }

    public function getID()
    {
        return $this->ID;
    }

    public function getFormName()
    {
        return $this->formName;
    }
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    public function setFormName($formName)
    {
        $this->formName = $formName;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    public function getDeleteSpam()
    {
        return $this->deleteSpam;
    }

    public function setDeleteSpam($deleteSpam)
    {
        $this->deleteSpam = $deleteSpam;
    }

    public function isDeleteSpam()
    {
        return $this->deleteSpam == 1;
    }

    public function getDecodedAttributes()
    {
        return json_decode($this->attributes);
    }

    /**
    * @PrePersist
    */
    public function prePersist()
    {
        $currentDate = new DateTime();
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


    public function getAttributeObjects()
    {
        $decodedAttrs = $this->getDecodedAttributes();
        $attrObjs = array();
        if($decodedAttrs){
            foreach ($decodedAttrs->formPages as $page){
                foreach ($page->attributes as $attr){
                    $attrObjs[$attr->akID] = AttributeKey::getInstanceByID($attr->akID);
                }
            }
        }
        return $attrObjs;
    }
}
