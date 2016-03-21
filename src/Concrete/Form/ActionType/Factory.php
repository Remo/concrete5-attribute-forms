<?php
namespace Concrete\Package\AttributeForms\Form\ActionType;

use Concrete\Package\AttributeForms\Form\ActionType\AbstractController;
use Concrete\Package\AttributeForms\Form\ActionType\View  as ActionTypeView;
use Concrete\Package\AttributeForms\Form\ActionType\Value as ActionTypeValue;
use Concrete\Package\AttributeForms\MeschApp;
use Exception,
    Core;

class Factory
{
    protected static $loc;
    
    protected $actions  = array();
    protected $app;

    protected function __construct()
    {
        $this->app = Core::getFacadeApplication();
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
     * Register Built-in payment methods
     */
    public function registerDefaults()
    {
        $this->register('mail', MeschApp::pkgHandle());
        $this->register('mail2', MeschApp::pkgHandle());
    }
    
    /**
     *
     * @param string $handle
     * @param string|Package $pkg
     */
    public function register($handle, $pkg = false)
    {
        $pkgHandle = $pkg;
        if(is_object($pkg)){
            $pkgHandle = $pkg->getPackageHandle();
        }

        $methodName = camelcase($handle);
        if($pkgHandle){
            $class = NAMESPACE_SEGMENT_VENDOR.'\\Package\\'.camelcase($pkgHandle)."\\ActionType\\{$methodName}\\Controller";
        }else{
            $class = NAMESPACE_APPLICATION."\\ActionType\\{$methodName}\\Controller";
        }

        $inst = $this->app->make($class);
        if(!($inst instanceof AbstractController)){
            throw new Exception(t('Form Action Type Class should be an instance of %s', 'Concrete\\Package\\AttributeForms\\Form\\ActionType\\AbstractController'));
        }
        
        $this->actions[$handle] = t(camelcase($handle));
        $this->app->bind("mesch/form/action/$handle", $class);
    }

    /**
     * Get all actions
     * @return array
     */
    public static function get()
    {
        return static::getInstance()->actions;
    }

   /**
     * Get Action Type By Handle
     * @param string $handle
     * @return \Concrete\Package\AttributeForms\Form\ActionType\AbstractController
     * @throws Exception
     */
    public static function getByHandle($handle)
    {
        $factory = static::getInstance();
        if (!isset($factory->actions[$handle])) {
            throw new Exception(t('Undefined Action Type given "%s"', $handle));
        }

        return $factory->app->make("mesch/form/action/$handle");
    }

    public static function execute(array $customActionValue, array $args = array())
    {
        $value  = new ActionTypeValue($customActionValue);
        $action = static::getByHandle($value->getActionType());
        $action->setValue($value);
        return call_user_func_array(array($action, 'execute'), $args);
    }

    public static function render($handle, $view, $value = false, $return = false)
    {
        $actioType = static::getByHandle($handle);
        if($value){
            $actioType->setValue($value);
        }
        $av = new ActionTypeView($actioType);

        ob_start();
        $av->render($view);
        $contents = ob_get_contents();
        ob_end_clean();
        
        if ($return) {
            return $contents;
        } else {
            print $contents;
        }
    }
}