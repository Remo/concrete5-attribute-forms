<?php
namespace Concrete\Package\AttributeForms\Src\Attribute\Value;

use Concrete\Core\Attribute\Value\Value,
    Loader,
    Concrete\Core\Support\Facade\Database,
    Concrete\Package\AttributeForms\Models\AttributeForm;

class AttributeFormValue extends Value
{
    /**
     * @var AttributeForm
     */
    private $item;

    public function setAttributeForm(AttributeForm $object)
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
        return null;
    }

    public function delete()
    {
        $db = Database::connection();
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