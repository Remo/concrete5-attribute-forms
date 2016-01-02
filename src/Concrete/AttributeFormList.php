<?php
namespace Concrete\Package\AttributeForms;

use Concrete\Package\AttributeForms\AbstractRepositoryList;
use Concrete\Package\AttributeForms\Entity\AttributeFormType;

/**
 * @method \Concrete\Package\AttributeForms\Entity\AttributeForm getOne()
 * @method \Concrete\Package\AttributeForms\Entity\AttributeForm[] getResults()
 */
class AttributeFormList extends AbstractRepositoryList
{
    
    protected function getEntityClassName()
    {
        return 'Concrete\Package\AttributeForms\Entity\AttributeForm';
    }

    public function filterByTypeID($aftID)
    {
        $this->criteria->andWhere($this->criteria->expr()->eq("typeID", $aftID));
    }

    public function filterByType(AttributeFormType $aft)
    {
        $this->filterByTypeID($aft->getID());
    }

    public function sortByDateCreated($dir = 'asc')
    {
        $this->addSortBy('dateCreated', $dir);
    }


}
