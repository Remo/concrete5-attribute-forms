<?php

namespace Concrete\Package\AttributeForms\Src;

use Concrete\Package\AttributeForms\Src\AbstractRepositoryList;
use Concrete\Package\AttributeForms\Src\Entity\AttributeFormType;

/**
 * @method \Concrete\Package\AttributeForms\Src\Entity\AttributeFormType getByID(mixed $id)
 * @method \Concrete\Package\AttributeForms\Src\Entity\AttributeFormType getOne()
 * @method \Concrete\Package\AttributeForms\Src\Entity\AttributeFormType[] getResults()
 */
class AttributeFormTypeList extends AbstractRepositoryList
{

    protected function getEntityClassName()
    {
        return 'Concrete\Package\AttributeForms\Src\Entity\AttributeFormType';
    }

    protected function getPackageHandle()
    {
        return 'attribute_forms';
    }

    public function sortByFormName($dir = 'asc')
    {
        $this->addSortBy('formName', $dir);
    }

    public function getArray()
    {
        $results   = array();
        $formTypes = $this->getResults();
        foreach ($formTypes as $formType) {
            $results[$formType->getID()] = $formType->getFormName();
        }
        return $results;
    }
}