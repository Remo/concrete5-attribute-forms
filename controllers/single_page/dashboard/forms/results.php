<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard\Forms;

use Concrete\Package\AttributeForms\Entity\AttributeFormType;
use Concrete\Package\AttributeForms\Entity\AttributeForm;
use Concrete\Package\AttributeForms\AttributeFormList;
use Concrete\Package\AttributeForms\AttributeFormTypeList;
use Concrete\Core\Page\Controller\DashboardPageController;
    

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
        header("Content-Type: application/vnd.ms-excel");
        header("Cache-control: private");
        header("Pragma: public");
        header("Content-Disposition: inline; filename=form_entries_{$aftID}.xls");
        header("Content-Title: Form Entries {$aftID}");

        $aft = AttributeFormType::getByID($aftID);
        $afl = new AttributeFormList();
        $afl->sortByDateCreated('desc');
        $afl->filterByType($aft);

        $attributes = $aft->getAttributeObjects();

        echo '<table>';

        // Add table header
        $headers = [t('ID'), t('Date Created')];

        foreach ($attributes as $attribute) {
            $headers[] = $attribute->getAttributeKeyDisplayName();
        }
        $entries = $afl->getResults();

        echo '<tr>';
        foreach ($headers as $header) {
            echo '<th>' . $header . '</th>';
        }
        echo '</tr>';

        // Add table content
        foreach ($entries as $entry) {
            echo '<tr>';
            echo '<td>' . $entry->getID() . '</td>';
            echo '<td>' . $entry->getDateCreated() . '</td>';

            foreach ($attributes as $attribute) {
                echo '<td>';
                echo $entry->getAttribute($attribute, 'display');
                echo '</td>';

            }

            echo '</tr>';
        }
        echo '</table>';
        die();

    }

    public function detail($afID)
    {
        $af = AttributeForm::getByID($afID);
        $aft = AttributeFormType::getByID($af->getTypeID());
        $attributes = $aft->getAttributeObjects();

        $this->set('af', $af);
        $this->set('afID', $afID);
        $this->set('aftID', $af->getTypeID());
        $this->set('attributes', $attributes);
    }
}
