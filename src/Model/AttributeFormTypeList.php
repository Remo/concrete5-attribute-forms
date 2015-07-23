<?php
namespace Concrete\Package\AttributeForms\Src\Model;

use Loader,
    Localization,
    User,
    Concrete\Core\Legacy\DatabaseItemList;

class AttributeFormTypeList extends DatabaseItemList
{
    private $queryCreated;

    protected function setBaseQuery()
    {
        $this->setQuery('SELECT aftID, deleteSpam, formName, dateCreated, dateUpdated FROM AttributeFormTypes');
    }

    protected function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = 1;
        }
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $formTypes = array();
        $this->createQuery();
        $r = parent::get($itemsToGet, $offset);
        foreach ($r as $row) {
            $formTypes[] = new AttributeFormType($row['aftID'], $row);
        }
        return $formTypes;
    }

    public function getTotal()
    {
        $this->createQuery();
        return parent::getTotal();
    }

    public static function getList()
    {
        $list = new self();
        $list->sortBy('formName');
        $formTypes = $list->get(0);
        $result = [];

        foreach ($formTypes as $formType) {
            $result[$formType->getID()] = $formType->getFormName();
        }

        return $result;
    }

    public function sortByFormName($dir = 'asc')
    {
        $this->sortBy('formName', $dir);
    }

}
