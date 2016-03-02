<?php

namespace Concrete\Package\AttributeForms\Service\Form;

use Exception,
    Closure,
    Core;

abstract class AbstractActionManager
{

    protected $actions  = array();

    protected function __construct()
    {

    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (null === static::$loc) {
            static::$loc = new static;
        }
        return static::$loc;
    }

    /**
     *
     * @param string $name
     * @param string|Closure $action if string "\Full\Class\Name::methodName"
     * One argument passed to action: object instance of AttributeForm
     */
    public function register($name, $action)
    {
        $this->actions[$name] = $action;
    }

    /**
     * Get all actions
     * @return array
     */
    public static function get()
    {
        return array_keys(static::getInstance()->actions);
    }

    protected static function run($name, array $args = array())
    {
        if (!isset(static::getInstance()->actions[$name])) {
            throw new Exception(t('Undefined Action given "%s"', $name));
        }

        $action = static::getInstance()->actions[$name];
        if ($action instanceof Closure) {
            $callable = $action;
        } else {
            $class      = reset(explode('::', $action));
            $methodName = end(explode('::', $action));
            $callable   = array(Core::make($class), $methodName);
        }

        return call_user_func_array($callable, $args);
    }
}