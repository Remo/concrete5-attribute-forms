<?php
namespace Concrete\Package\AttributeForms\Src;

use Concrete\Package\AttributeForms\Src\AbstractRepositoryList;
use Concrete\Package\AttributeForms\Src\Entity\AttributeFormType;

/**
 * @method \Concrete\Package\AttributeForms\Src\Entity\AttributeForm getByID(mixed $id)
 * @method \Concrete\Package\AttributeForms\Src\Entity\AttributeForm getOne()
 * @method \Concrete\Package\AttributeForms\Src\Entity\AttributeForm[] getResults()
 */
class AttributeFormList extends AbstractRepositoryList
{
    
    protected function getEntityClassName()
    {
        return 'Concrete\Package\AttributeForms\Src\Entity\AttributeForm';
    }

    protected function getPackageHandle()
    {
        return 'attribute_forms';
    }

    public function filterByTypeID($aftID)
    {
        $this->criteria->andWhere($this->criteria->expr()->eq("aftID", $aftID));
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
