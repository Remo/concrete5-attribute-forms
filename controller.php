<?php
namespace Concrete\Package\AttributeForms;

use Concrete\Core\Backup\ContentImporter,
    Package;

class Controller extends Package
{
    protected $pkgHandle = 'attribute_forms';
    protected $appVersionRequired = '5.7.4.2';
    protected $pkgVersion = '0.9.3';

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

}