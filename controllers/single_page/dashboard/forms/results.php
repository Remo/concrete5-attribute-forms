<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard\Forms;

use Concrete\Package\AttributeForms\Entity\AttributeFormType;
use Concrete\Package\AttributeForms\Entity\AttributeForm;
use Concrete\Package\AttributeForms\AttributeFormList;
use Concrete\Package\AttributeForms\AttributeFormTypeList;
use Concrete\Core\Page\Controller\DashboardPageController;
    
use Core;

class Results extends DashboardPageController
{
    protected $helpers = array('form', 'date');

    public function view()
    {
        $aftl = new AttributeFormTypeList();
        $aftl->sortByFormName();
        $this->set('formTypes', $aftl->getPage());

        $pagination = $aftl->getPagination();
        if ($pagination->haveToPaginate()) {
            $this->set('formTypesPagination', $pagination->renderDefaultView());
            $this->requireAsset('css', 'core/frontend/pagination');
        }
    }

    public function entries($aftID)
    {
        $aft = AttributeFormType::getByID($aftID);
        $afLst = new AttributeFormList();
        $afLst->filterByType($aft);
        $afLst->sortByDateCreated('desc');
        
        $this->set('showSpam', !$aft->getDeleteSpam());
        $this->set('formName', $aft->getFormName());
        $this->set('forms', $afLst->getPage());

        $pagination = $afLst->getPagination();
        if ($pagination->haveToPaginate()) {
            $this->set('formsPagination', $pagination->renderDefaultView());
            $this->requireAsset('css', 'core/frontend/pagination');
        }
    }

    public function excel($aftID)
    {
        $aft = AttributeFormType::getByID($aftID);
        $afl = new AttributeFormList();
        $afl->sortByDateCreated('desc');
        $afl->filterByType($aft);
        $entries = $afl->getResults();

        $excelExport = Core::make('helper/excel/export'); /* @var $excelExport \Concrete\Package\AttributeForms\Service\Excel\Export */
        $date       = Core::make('helper/date');
        
        // Add table header
        $headers    = array(t('ID'), t('Date Created'));
        $attributes = $aft->getAttributeObjects();
        foreach ($attributes as $attribute) {
            $headers[] = $attribute->getAttributeKeyDisplayName();
        }

        // Add table content
        $data = array();
        foreach ($entries as $entry) {
            $row = array($entry->getID(), $date->formatDateTime($entry->getDateCreated()));
            foreach ($attributes as $attribute) {
                $row[] = $entry->getAttribute($attribute, 'display');
            }
            $data[] = $row;
        }

        $excelExport->addTabContent(t('Form Entries - %s', $aft->getFormName()), $headers, $data);
        $excelExport->download("form_entries_{$aftID}");
        die();
    }

    public function detail($afID)
    {
        $af = AttributeForm::getByID($afID);
        $aft = $af->getTypeObj();
        $attributes = $aft->getAttributeObjects();

        $this->set('af', $af);
        $this->set('afID', $afID);
        $this->set('aftID', $af->getTypeID());
        $this->set('attributes', $attributes);
    }
}
