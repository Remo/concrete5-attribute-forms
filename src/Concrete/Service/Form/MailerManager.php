<?php
namespace Concrete\Package\AttributeForms\Service\Form;

class MailerManager
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
     * @param Closure $action with 2 args
     * Example:
     * <code>
     *  $instance->register('ActionName', function($app, $args){
     *      // Your custom code
     *      // $args array(\Concrete\Package\AttributeForms\Entity\AttributeForm)
     *  });
     * </code>
     */
    public function register($name, Closure $action)
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

    public static function runAction($name, $parameters = array())
    {

        if (!isset(static::getInstance()->actions[$name])) {
            throw new Exception(t('Undefined Action given "%s"', $name));
        }
        return Core::make(static::getInstance()->actions[$name], $parameters);
    }
}