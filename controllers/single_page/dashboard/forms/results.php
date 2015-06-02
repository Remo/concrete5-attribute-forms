<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard\Forms;

use Concrete\Package\AttributeForms\Models\AttributeForm,
    Concrete\Package\AttributeForms\Models\AttributeFormList,
    Concrete\Package\AttributeForms\Models\AttributeFormTypeList,
    PageController,
    Loader,
    Page,
    Concrete\Package\AttributeForms\Models\AttributeFormType;

class Results extends PageController
{
    protected $helpers = array('form');

    public function view()
    {
        $currentPage = Page::getCurrentPage();
        $aftl = new AttributeFormTypeList();
        $this->set('formTypes', $aftl->getPage());
        $this->set('formTypesPagination', $aftl->displayPagingV2(Loader::helper('navigation')->getLinkToCollection($currentPage), true));
    }

    public function entries($aftID) {
        $currentPage = Page::getCurrentPage();
        $aft = AttributeFormType::getByID($aftID);
        $afl = new AttributeFormList();
        $afl->filterByType($aft);
        $this->set('formName', $aft->getFormName());
        $this->set('forms', $afl->getPage());
        $this->set('formsPagination', $afl->displayPagingV2(Loader::helper('navigation')->getLinkToCollection($currentPage), true));
    }

    public function detail($afID)
    {
        $af = AttributeForm::getByID($afID);
        $aft = AttributeFormType::getByID($af->getTypeID());
        $attributes = $aft->getAttributeObjects();

        $this->set('af', $af);
        $this->set('afID', $afID);
        $this->set('attributes', $attributes);
    }
}