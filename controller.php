<?php
namespace Concrete\Package\AttributeForms;

use Concrete\Core\Foundation\ModifiedPsr4ClassLoader;
use Concrete\Package\AttributeForms\Form\ActionType\Factory as ActionTypeFactory;
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
    protected $pkgVersion = '0.9.9.5';

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
        define('DIRNAME_ACTION_TYPE', 'action_types');
        $this->registerAutoloaderMapping();
        $this->registerAssets();

        $list = new ProviderList(Core::getFacadeApplication());
        $list->registerProvider('\Concrete\Package\AttributeForms\Service\Provider');

        Route::registerMultiple(array(
            '/ccm/attribute_forms/tools/captcha/{atFormTypeID}' => array('\Concrete\Package\AttributeForms\Controller\Tools::displayCaptchaPicture'),
            '/ccm/attribute_forms/tools/form/action_type/{handle}/{action}' => array('\Concrete\Package\AttributeForms\Controller\Tools::formActionTypeAction'),
            '/ccm/attribute_forms/tools/form/action_type/render/{handle}/{view}' => array('\Concrete\Package\AttributeForms\Controller\Tools::renderFormActionType')
        ));

        ActionTypeFactory::getInstance()->registerDefaults();
    }

    protected function registerAutoloaderMapping()
    {
        if (file_exists($this->getPackagePath().'/vendor/autoload.php')) {
            require_once $this->getPackagePath().'/vendor/autoload.php';
        }

        if (file_exists(DIR_BASE.'/vendor/autoload.php')) {
            require_once DIR_BASE.'/vendor/autoload.php';
        }

        $namespace       = 'Application';
        $app_config_path = DIR_APPLICATION.'/config/app.php';
        if (file_exists($app_config_path)) {
            $app_config = require $app_config_path;
            if (isset($app_config['namespace'])) {
                $namespace = $app_config['namespace'];
            }
        }
        define('NAMESPACE_APPLICATION', $namespace);

        $symfonyLoader = new ModifiedPsr4ClassLoader();
        $symfonyLoader->addPrefix($namespace.'\\ActionType', DIR_APPLICATION.'/'.DIRNAME_ACTION_TYPE);

        $packages = \Concrete\Core\Package\PackageList::get()->getPackages();
        foreach ($packages as $pkg){
            $pkgHandle = $pkg->getPackageHandle();
            $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR.'\\Package\\'.camelcase($pkgHandle).'\\ActionType', DIR_PACKAGES."/{$pkgHandle}/".DIRNAME_ACTION_TYPE);
        }
        $symfonyLoader->register();
    }

    protected function registerAssets()
    {
        $al  = AssetList::getInstance();
        $al->register('javascript', 'mesch/attribute_form', 'js/attribute.forms.js', array('minify' => true), $this);
        $al->register('css', 'mesch/attribute_form/backend', 'css/backend.css', array('minify' => true), $this);
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