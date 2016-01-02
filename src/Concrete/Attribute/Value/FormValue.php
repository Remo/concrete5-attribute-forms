<?php
namespace Concrete\Package\AttributeForms\Attribute\Value;

use Concrete\Core\Attribute\Value\Value as AttributeValue;
use Concrete\Package\AttributeForms\Entity\AttributeForm;
use Database;

class FormValue extends AttributeValue
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
        $db->delete('AttributeFormsAttributeValues', array(
            'afID' => $this->item->getID(),
            'akID' => $this->attributeKey->getAttributeKeyID(),
            'avID' => $this->getAttributeValueID()
        ));

        // Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
        $num = $db->fetchColumn('select count(avID) from AttributeFormsAttributeValues where avID = ?', array($this->getAttributeValueID()));
        if ($num < 1) {
            parent::delete();
        }
    }
}
