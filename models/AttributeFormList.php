<?php
namespace Concrete\Package\AttributeForms\Models;

use Loader,
    Localization,
    User,
    Concrete\Core\Legacy\DatabaseItemList;

class AttributeFormList extends DatabaseItemList
{
    private $queryCreated;

    protected function setBaseQuery()
    {
        $this->setQuery('SELECT afID, aftID, dateCreated, dateUpdated, isSpam FROM AttributeForms');
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
        $forms = array();
        $this->createQuery();
        $r = parent::get($itemsToGet, $offset);
        foreach ($r as $row) {
            $forms[] = new AttributeForm($row['afID'], $row);
        }
        return $forms;
    }

    public function getTotal()
    {
        $this->createQuery();
        return parent::getTotal();
    }

    public function filterByTypeID($aftID)
    {
        $this->filter('aftID', $aftID);
    }

    public function filterByType(AttributeFormType $aft)
    {
        $this->filterByTypeID($aft->getID());
    }

    public function sortByDateCreated($dir = 'asc')
    {
        $this->sortBy('dateCreated', $dir);
    }

}