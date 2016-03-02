<?php
namespace Concrete\Package\AttributeForms;

use Concrete\Core\Foundation\Service\ProviderList;
use Concrete\Core\Backup\ContentImporter,
    AssetList,
    Package,
    Route,
    Core;

class Controller extends Package
{
    protected $pkgHandle = 'attribute_forms';
    protected $appVersionRequired = '5.7.5.2';
    protected $pkgVersion = '0.9.9.4';

    protected $pkgAutoloaderMapCoreExtensions = true;
    
    public function getPackageName()
    {
        return t('Attribute Form');
    }

    public function getPackageDescription()
    {
        return t('A package to create forms using attributes');
    }

    protected function installXmlContent()
    {
        $pkg = Package::getByHandle($this->pkgHandle);

        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install.xml');
    }

    public function install()
    {
        parent::install();
        $this->installXmlContent();
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installXmlContent();
    }

    public function on_start()
    {
        if (file_exists($this->getPackagePath().'/vendor/autoload.php')) {
            require_once $this->getPackagePath().'/vendor/autoload.php';
        }

        if (file_exists(DIR_BASE.'/vendor/autoload.php')) {
            require_once DIR_BASE.'/vendor/autoload.php';
        }

        $list = new ProviderList(Core::getFacadeApplication());
        $list->registerProvider('\Concrete\Package\AttributeForms\Service\Provider');

        $this->registerAssets();

        Route::registerMultiple(array(
            '/ccm/attribute_forms/tools/captcha/{atFormTypeID}' => array('\Concrete\Package\AttributeForms\Controller\Tools::displayCaptchaPicture'),
        ));
        
        // Test
        Service\Form\ActionManager::getInstance()->register('Test Custom Action', function(Entity\AttributeForm $form){
            $v = \View::getInstance();
            $v->addFooterItem('<script>alert("Custom Action Executed For form '.$form->getTypeName().'");</script>');
        });
        Service\Form\MailerManager::getInstance()->register('Test Custom Mail Handler', '\Concrete\Package\AttributeForms\MeschApp::sendMail');
    }

    private function registerAssets()
    {
        $al  = AssetList::getInstance();
        $al->register('javascript', 'mesch/attribute_form', 'js/attribute.forms.js', array('minify' => true), $this);
    }
    
    public function uninstall()
    {
        parent::uninstall();
        $db = \Database::connection();
        
        $platform = $db->getDatabasePlatform();
        $db->executeQuery($platform->getDropTableSQL('btAttributeForm'));
        $db->executeQuery($platform->getDropTableSQL('AttributeFormsAttributeValues'));
        $db->executeQuery($platform->getDropTableSQL('AttributeForms'));
        $db->executeQuery($platform->getDropTableSQL('AttributeFormTypes'));
        $db->executeQuery($platform->getDropTableSQL('atAttributeSwitcher'));
        $db->executeQuery($platform->getDropTableSQL('atAttributeSwitcherSettings'));
        $db->executeQuery($platform->getDropTableSQL('AttributeFormsIndexAttributes'));
    }
}