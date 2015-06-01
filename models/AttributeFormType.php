<?php
namespace Concrete\Package\AttributeForms\Models;

use Concrete\Core\Foundation\Object,
    Concrete\Core\Support\Facade\Database,
    Concrete\Package\AttributeForms\Src\Attribute\Key\AttributeFormKey,
    Loader,
    Localization,
    PermissionKey;

class AttributeFormType extends Object
{
    protected $aftID;

    public function __construct($aftID, $row = null)
    {
        $this->aftID = $aftID;
        if ($row == null) {
            $db = Database::connection();
            $row = $db->GetRow('SELECT * FROM AttributeFormTypes WHERE aftID=?', array($aftID));
        }
        $this->setPropertiesFromArray($row);
    }

    public static function getByID($aftID)
    {
        return new self($aftID);
    }

    public function update($data)
    {
        $db = Database::connection();
        $db->update('AttributeFormTypes', $data, ['aftID' => $this->aftID]);
    }

    public static function add($data)
    {
        $db = Database::connection();
        $data['dateCreated'] = date('Y-m-d H:i:s');
        $data['dateUpdated'] = $data['dateCreated'];
        $db->insert('AttributeFormTypes', $data);
        return new self($db->Insert_ID(), $data);
    }

    public function remove()
    {
        $db = Database::connection();
        $db->Execute('DELETE FROM AttributeFormTypes WHERE afID=?', array($this->aftID));
    }

    public function getID()
    {
        return $this->aftID;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    public function getFormName()
    {
        return $this->formName;
    }

    public function getAttributeObjects()
    {
        $attributes = $this->getAttributes();
        $result = [];
        foreach ($attributes as $akID) {
            $afk = AttributeFormKey::getByID($akID);
            $result[] = $afk;
        }
        return $result;
    }

    public function getAttributes()
    {
        $db = Database::connection();
        return $db->GetCol('SELECT akID FROM AttributeFormTypeAttributes WHERE aftID = ? ORDER BY sortOrder',
            array($this->getID()));
    }


    public function setAttributes($attributes)
    {
        if (is_array($attributes)) {
            $db = Database::connection();
            $db->Execute('DELETE FROM AttributeFormTypeAttributes WHERE aftID = ?',
                array($this->getID()));
            $sortOrder = 1;
            foreach ($attributes as $akID) {
                $data = [
                    'aftID' => $this->getID(),
                    'akID' => $akID,
                    'sortOrder' => $sortOrder++,
                ];
                $db->insert('AttributeFormTypeAttributes', $data);
            }
        }
    }

}