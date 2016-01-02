<?php

namespace Concrete\Package\AttributeForms;

use Concrete\Package\AttributeForms\AbstractRepositoryList;

/**
 * @method \Concrete\Package\AttributeForms\Entity\AttributeFormType getOne()
 * @method \Concrete\Package\AttributeForms\Entity\AttributeFormType[] getResults()
 */
class AttributeFormTypeList extends AbstractRepositoryList
{

    protected function getEntityClassName()
    {
        return 'Concrete\Package\AttributeForms\Entity\AttributeFormType';
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