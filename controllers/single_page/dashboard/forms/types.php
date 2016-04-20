<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard\Forms;

use Concrete\Package\AttributeForms\Attribute\Options as AttributeOptions;
use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;
use Concrete\Package\AttributeForms\AttributeFormTypeList;
use Concrete\Package\AttributeForms\Entity\AttributeFormType;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Captcha\Library as SystemCaptchaLibrary;
use Database,
    Core;

class Types extends DashboardPageController
{
    protected $helpers = array('form');

    public function view()
    {
        $aftl = new AttributeFormTypeList();
        $this->set('formTypes', $aftl->getPage());

        $pagination = $aftl->getPagination();
        if ($pagination->haveToPaginate()) {
            $this->set('formTypesPagination', $pagination->renderDefaultView());
            $this->requireAsset('css', 'core/frontend/pagination');
        }
        $this->requireAsset('mesch/alert');
        $this->requireAsset('javascript', 'mesch/attribute_form');
    }

    protected function getAttributeKeys()
    {
        $list = AttributeFormKey::getList();
        $attributes = array();
        foreach ($list as $ak) {
            $item = new \stdClass();
            $item->akID = $ak->getAttributeKeyID();
            $item->akName = $ak->getAttributeKeyDisplayName();
            $item->atHandle = $ak->getAttributeTypeHandle();
            $attributes[$ak->getAttributeKeyHandle()] = $item;
        }
        return $attributes;
    }

    public function add()
    {
        $this->requireAsset('javascript', 'underscore');
        $this->requireAsset('redactor');
        $this->requireAsset('core/file-manager');
        $this->requireAsset('javascript', 'mesch/attribute_form');

        $list     = SystemCaptchaLibrary::getList();
        $captchas = array('' => t('Default'));
        foreach ($list as $sc) {
            $captchas[$sc->getSystemCaptchaLibraryHandle()] = $sc->getSystemCaptchaLibraryName();
        }

        $this->set('captchasLibraries', $captchas);
        $this->set('attributeOptions', AttributeOptions::get());
        $this->set('attributeKeys', $this->getAttributeKeys());
    }

    public function edit($aftID)
    {
        $this->add();
        $attributeForm = AttributeFormType::getByID($aftID);

        $selectedAttributes = $attributeForm->getDecodedAttributes(
                $includeAtHandle = true /* needed to determine attribute options */
        );
        
        $this->set('selectedAttributes', $selectedAttributes);
        $this->set('attributeForm', $attributeForm);
    }

    public function layout($aftID)
    {
        $this->add();
        $attributeForm = AttributeFormType::getByID($aftID);

        $selectedAttributes = $attributeForm->getLayoutDecodedAttributes(
            $includeAtHandle = true /* needed to determine attribute options */
        );
        $attributesHtml = $attributeForm->getAttributesHtml(
            $includeAtHandle = true /* needed to determine attribute options */
        );
        $this->set('selectedAttributes', $selectedAttributes);
        $this->set('attributesHtml', $attributesHtml);
        $this->set('attributeForm', $attributeForm);
        $this->requireAsset('javascript', 'mesch/gridmanager');
        $this->requireAsset('css', 'mesch/gridmanagercss');
    }

    public function save($aftID = 0)
    {
        $formName       = $this->post('formName');
        $deleteSpam     = $this->post('deleteSpam', 0);
        $captchaLibrary = $this->post('captchaLibrary');

        if ($aftID > 0) {
            $attributeFormType = AttributeFormType::getByID($aftID);
        } else {
            $attributeFormType = new AttributeFormType();
        }
        
        $attributeFormType->setFormName($formName);
        $attributeFormType->setDeleteSpam($deleteSpam);
        $attributeFormType->setCaptchaLibraryHandle($captchaLibrary);
        $attributeFormType->setAttributes($this->post('attributes'));
        $attributeFormType->save();

        if ($aftID > 0) {
            $this->flash("message", t('Form type updated'));
        }else{
            $this->flash("message", t('Form type added'));
        }
        $this->redirect($this->action(''));
    }

    public function saveLayout($aftID = 0)
    {
        if ($aftID > 0) {
            $attributeFormType = AttributeFormType::getByID($aftID);
        } else {
            $attributeFormType = new AttributeFormType();
        }

        $attributeFormType->setLayoutAttributes($this->post('layout_attributes'));
        $attributeFormType->save();

        if ($aftID > 0) {
            $this->flash("message", t('Form type updated'));
        }else{
            $this->flash("message", t('Form type added'));
        }
        $this->redirect($this->action(''));
    }

    public function delete($aftID, $ccmToken)
    {
        $token = Core::make('token');
        if(!$token->validate('delete_ft', $ccmToken)){
            $this->error->add($token->getErrorMessage());
            $this->flash("error", $this->error);
            $this->redirect($this->action(''));
            exit();
        }

        $formType = AttributeFormType::getByID($aftID);
        
        // Check if requested form type is already in use
        $qb = Database::connection()->createQueryBuilder();
        $qb->select('count(*)')->from('btAttributeForm')->where($qb->expr()->eq('aftID', ':aftID'))
            ->setParameter('aftID', $aftID);

        $num = $qb->execute()->fetchColumn();
        if($num > 0){
            $this->error->add(t('The Form Type "%s" is already in use, please update or delete related blocks and try again.', $formType->getFormName()));
            $this->flash("error", $this->error);
            $this->redirect($this->action(''));
            exit();
        }

        $formType->delete();
        $this->flash("message", t('Form type deleted'));
        $this->redirect($this->action(''));
    }
}
