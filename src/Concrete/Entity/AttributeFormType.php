<?php
namespace Concrete\Package\AttributeForms\Entity;

use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;
use \Concrete\Core\Captcha\Library as SystemCaptchaLibrary;
use DateTime;


/**
 * @Entity
 * @Table(name="AttributeFormTypes")
 * @HasLifecycleCallbacks
 *
 * @method AttributeFormType getByID(mixed $id)
 */
class AttributeFormType extends EntityBase
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
     * @Column(type="text",  nullable=true)
     */
    protected $layoutAttributes;

    /**
     * @Column(type="boolean")
     */
    protected $deleteSpam;

    /**
     * @Column(type="string")
     */
    protected $captchaLibraryHandle;

    /**
     * @Column(type="datetime")
     */
    protected $dateCreated;

    /**
     * @Column(type="datetime")
     */
    protected $dateUpdated;

    
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

    public function getLayoutAttributes()
    {
        return $this->layoutAttributes;
    }

    public function getCaptchaLibraryHandle()
    {
        return $this->captchaLibraryHandle;
    }

    public function getCaptchaLibrary()
    {
        if (!empty($this->captchaLibraryHandle)) {
            $captcha = SystemCaptchaLibrary::getByHandle($this->getCaptchaLibraryHandle());
        } else {
            $captcha = SystemCaptchaLibrary::getActive();
        }
        
        return $captcha->getController();
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

    public function setLayoutAttributes($attributes)
    {
        $this->layoutAttributes = $attributes;
    }

    public function setCaptchaLibraryHandle($captchaLibraryHandle)
    {
        $this->captchaLibraryHandle = $captchaLibraryHandle;
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

    /**
     * Get attributes as json object
     * @param boolean $includeAtHandle include attribute type handle
     * @return object
     */
    public function getDecodedAttributes($includeAtHandle = false)
    {
        $selectedAttributes = json_decode($this->attributes);
        // Include attribute type handle
        if ($includeAtHandle && is_object($selectedAttributes)) {
            foreach ($selectedAttributes->formPages as $page) {
                foreach ($page->attributes as $attr) {
                    $ak = AttributeFormKey::getByID($attr->akID);
                    if (is_object($ak)) {
                        $attr->atHandle = $ak->getAttributeTypeHandle();
                    }
                }
            }
        }

        return $selectedAttributes;
    }

    /**
     * Get attributes as json object
     * @param boolean $includeAtHandle include attribute type handle
     * @return object
     */
    public function getLayoutDecodedAttributes($includeAtHandle = false)
    {
        $selectedAttributes = json_decode($this->layoutAttributes);

        // Include attribute type handle
        if ($includeAtHandle && is_object($selectedAttributes)) {
            foreach ($selectedAttributes->formPages as $pagess) {
                if($pagess) {
                    foreach ($pagess as $pages) {
                        if (!empty($pages) && is_array($pages)) {
                            foreach ($pages as $page) {
                                if (!empty($page) && isset($page->attributes)) {
                                    foreach ($page->attributes as $attr) {
                                        $ak = AttributeFormKey::getByID($attr->akID);
                                        if (is_object($ak)) {
                                            $attr->atHandle = $ak->getAttributeTypeHandle();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $selectedAttributes;
    }

    public function getAttributesByAttrType($atHandle, $hasOption = false)
    {
        $selectedAttributes = json_decode($this->attributes);
        $attrs = array();
        if (is_object($selectedAttributes)) {
            foreach ($selectedAttributes->formPages as $page) {
                foreach ($page->attributes as $attr) {
                    $ak = AttributeFormKey::getByID($attr->akID);
                    if (is_object($ak) && $ak->getAttributeTypeHandle() == $atHandle) {
                        if($hasOption){
                            if(isset($attr->options->$hasOption) && $attr->options->$hasOption == true){
                                $attrs[$attr->akID] = $attr;
                            }
                        }else{
                            $attrs[$attr->akID] = $attr;
                        }
                    }
                }
            }
        }

        return $attrs;
    }

    /**
     * Get form page by handle
     * @param string $pageHandle urlifyed page name
     * @return array
     */
    public function getFormPage($pageHandle)
    {
        $selectedAttributes = json_decode($this->attributes);
        $formPage           = false;
        if (is_object($selectedAttributes)) {
            $txt = \Core::make('helper/text');
            foreach ($selectedAttributes->formPages as $i => $page) {
                if ($pageHandle == $txt->urlify($page->name)) {
                    $formPage         = clone $page;
                    $formPage->step   = $i + 1;
                    $formPage->handle = $pageHandle;
                    break;
                }
            }
        }
        return $formPage;
    }

    /**
     * Get next form page of given handle
     * @param string $pageHandle urlifyed page name
     * @return array
     */
    public function getNextFormPage($pageHandle)
    {
        $selectedAttributes = json_decode($this->attributes);
        $formPage           = false;
        if (is_object($selectedAttributes)) {
            $txt   = \Core::make('helper/text');
            $found = false;
            foreach ($selectedAttributes->formPages as $i => $page) {
                if ($found) {
                    $formPage         = clone $page;
                    $formPage->step   = $i + 1;
                    $formPage->handle = $txt->urlify($page->name);
                    break;
                } elseif ($pageHandle == $txt->urlify($page->name)) {
                    $found = true;
                }
            }
        }
        return $formPage;
    }

    /**
     * Get previous form page of given page handle
     * @param string $pageHandle urlifyed page name
     * @return array
     */
    public function getPrevFormPage($pageHandle)
    {
        $selectedAttributes = json_decode($this->attributes);
        $formPage           = false;
        if (is_object($selectedAttributes)) {
            $txt   = \Core::make('helper/text');
            $found = false;
            for ($i = count($selectedAttributes->formPages) - 1; $i >= 0; $i--) {
                $page = $selectedAttributes->formPages[$i];
                if ($found) {
                    $formPage         = clone $page;
                    $formPage->step   = $i + 1;
                    $formPage->handle = $txt->urlify($page->name);
                    break;
                } elseif ($pageHandle == $txt->urlify($page->name)) {
                    $found = true;
                }
            }
        }
        return $formPage;
    }

    public function getFirstFormPage()
    {
        $selectedAttributes = json_decode($this->attributes);
        $formPage           = false;
        if (is_object($selectedAttributes) && !empty($selectedAttributes->formPages)) {
            $formPage         = reset($selectedAttributes->formPages);
            $formPage->step   = 1;
            $formPage->handle = \Core::make('helper/text')->urlify($formPage->name);
        }
        return $formPage;
    }

    public function getLastFormPage()
    {
        $selectedAttributes = json_decode($this->attributes);
        $formPage           = false;
        if (is_object($selectedAttributes) && !empty($selectedAttributes->formPages)) {
            $formPage         = end($selectedAttributes->formPages);
            $formPage->step   = count($selectedAttributes->formPages);
            $formPage->handle = \Core::make('helper/text')->urlify($formPage->name);
        }
        return $formPage;
    }

    public function getFormPages()
    {
        $selectedAttributes = json_decode($this->attributes);
        if (is_object($selectedAttributes)) {
            return $selectedAttributes->formPages;
        }
        return [];
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


    /**
     * Return all used attribute keys
     * @return AttributeFormKey[]
     */
    public function getAttributeObjects()
    {
        $decodedAttrs = $this->getDecodedAttributes();
        $attrObjs = array();
        if($decodedAttrs){
            foreach ($decodedAttrs->formPages as $page){
                foreach ($page->attributes as $attr){
                    $attrObjs[$attr->akID] = AttributeFormKey::getByID($attr->akID);
                }
            }
        }
        return $attrObjs;
    }


    /**
     * Return all used attribute keys
     * @return AttributeFormKey[]
     */
    public function getLayoutAttributeObjects()
    {
        $decodedAttrs = $this->getLayoutDecodedAttributes();
        $attrObjs = array();

        if($decodedAttrs){
            foreach ($decodedAttrs->formPages as $row => $formPageRow){
                if(is_array($formPageRow)) {
                    foreach ($formPageRow as $col => $formPageCol) {
                        if (is_object($formPageCol)) {
                            foreach ((array)$formPageCol as $key => $formPage) {
                                if (is_array($formPage)) {
                                    foreach ($formPage as $attr) {
                                        $attrObjs[$attr->akID] = AttributeFormKey::getByID($attr->akID);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $attrObjs;
    }
}
