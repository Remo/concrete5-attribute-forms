<?php

namespace Concrete\Package\AttributeForms\Entity;

use Doctrine\DBAL\LockMode;
use Concrete\Package\AttributeForms\MeschApp;

abstract class EntityBase
{
    /** @var \Concrete\Core\Error\Error */
    public $error;

   /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed    $id          The identifier.
     * @param int      $lockMode    The lock mode.
     * @param int|null $lockVersion The lock version.
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public static function getByID($id, $lockMode = LockMode::NONE, $lockVersion = null)
    {
        return MeschApp::em()->getRepository(get_called_class())->find($id, $lockMode, $lockVersion);
    }

    public function save($flush = true, $merge = false)
    {
        $em = MeschApp::em();
        if ($merge) {
            $em->merge($this);
        } else {
            $em->persist($this);
        }

        if ($flush) {
            $em->flush();
        }
    }

    public function delete($flush = true)
    {
        $em = MeschApp::em();
        $em->remove($this);
        if ($flush) {
            $em->flush();
        }
    }

    function isError()
    {
        $args = func_get_args();
        if (isset($args[0]) && $args[0]) {
            return $this->error == $args[0];
        } else {
            return $this->error;
        }
    }

    /**
     *
     * @return \Concrete\Core\Error\Error
     */
    function getError()
    {
        return $this->error;
    }

    public function setPropertiesFromArray($arr)
    {
        foreach ($arr as $key => $prop) {
            $setter = 'set'.ucfirst($key);
            // we prefer passing by setter method
            if (method_exists($this, $setter)) {
                call_user_func(array($this, $setter), $prop);
            } else {
                $this->{$key} = $prop;
            }
        }
    }
}