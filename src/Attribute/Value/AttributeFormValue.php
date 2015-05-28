<?php
namespace Concrete\Package\AttributeForms\Src\Attribute\Value;

use Concrete\Core\Attribute\Value\Value,
    Loader;

class AttributeFormValue extends Value
{

    public function setAttributeForm($object)
    {
        $this->item = $object;
    }

    public static function getByID($avID)
    {
        $cav = new self();
        $cav->load($avID);
        if ($cav->getAttributeValueID() == $avID) {
            return $cav;
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute('delete from AttributeFormsAttributeValues where afID = ? and akID = ? and avID = ?', array(
            $this->item->getID(),
            $this->attributeKey->getAttributeKeyID(),
            $this->getAttributeValueID()
        ));

        // Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
        $num = $db->GetOne('select count(avID) from AttributeFormsAttributeValues where avID = ?',
            array($this->getAttributeValueID()));
        if ($num < 1) {
            parent::delete();
        }
    }
}