<?php

namespace Concrete\Package\AttributeForms\Service\Form;

use Concrete\Package\AttributeForms\Entity\AttributeForm;
use Closure,
    Core;

class ActionManager
{
    /**
     * @var null|self
     */
    private static $loc = null;
    protected $actions  = array();

    private function __construct()
    {

    }

    /**
     * @return ActionManager
     */
    public static function getInstance()
    {
        if (null === self::$loc) {
            self::$loc = new self;
        }
        return self::$loc;
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

    public static function runAction($name, AttributeForm $form)
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

        return call_user_func($callable, $form);
    }
}