<?php

namespace Concrete\Package\AttributeForms;

use Package;

class MeschApp
{
    private static $pkg;
    private static $em;

    /**
     * Get current package handle
     *
     * @return string
     */
    public static function pkgHandle()
    {
        return 'attribute_forms';
    }

    /**
     * Get current package object
     * 
     * @return \Package
     */
    public static function pkg()
    {
        if (!is_object(self::$pkg)) {
            self::$pkg = Package::getByHandle(self::pkgHandle());
        }
        return self::$pkg;
    }

    /**
     * Gets a package specific entity manager.
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public static function em()
    {
        if (!is_object(self::$em)) {
            self::$em = self::pkg()->getEntityManager();
        }
        return self::$em;
    }

    /**
     * Get the standard database config liaison
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public static function cfg()
    {
        return self::pkg()->getConfig();
    }

    /**
     * Get the standard filesystem config liaison
     *
     * <code>
     *    Config File Path: /application/config/generated_overrides/package_handle/mesch.php
     *    Conent e.g
     *    return array( <br>
     *            "email" => array(
     *                     "address" => "youremail.com",
     *                     "name" => "some"
     *                  )
     *            );
     * </code>
     *
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public static function getFileConfig()
    {
        return self::pkg()->getFileConfig();
    }

}