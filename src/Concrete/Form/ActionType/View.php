<?php
namespace Concrete\Package\AttributeForms\Form\ActionType;

use Concrete\Package\AttributeForms\MeschApp;
use Concrete\Core\View\AbstractView;
use View as ConcreteView;
use Environment,
    URL;

class View extends AbstractView {

    protected $pkgHandle;

    protected $initialViewToRender;
    protected $viewToRender;

    protected function constructView($actionType)
    {
        $this->setController($actionType);
        $this->pkgHandle  = $actionType->getPackageHandle();
    }

    public function start($state)
    {
        $this->initialViewToRender = $state;
        $this->viewToRender = $state;
        $env = Environment::get();
        $atHandle = $this->controller->getHandle();
        $r = $env->getRecord(DIRNAME_ACTION_TYPE.'/'.$atHandle.'/'.$this->viewToRender.'.php', $this->pkgHandle);
        if ($this->initialViewToRender == 'composer' && !$r->exists()) {
            $this->viewToRender = 'form';
        }
    }

    public function setupRender()
    {
        $this->runControllerTask();
        $atHandle = $this->controller->getHandle();
        $env      = Environment::get();
        $r        = $env->getRecord(DIRNAME_ACTION_TYPE.'/'.$atHandle.'/'.$this->viewToRender.'.php', $this->pkgHandle);
        $file     = $r->file;
        $this->setViewTemplate($file);
    }

    public function runControllerTask()
    {
        if(method_exists($this->controller, 'on_start')){
            $this->controller->on_start();
        }
        
        $action = $this->initialViewToRender;
        if ($action == 'composer' && !method_exists($this->controller, 'composer')) {
            $action = 'form';
        }
        $this->controller->runAction($action);
    }

    public function action($action)
    {
        $a = func_get_args();
        $args = '?';
        for ($i = 1; $i < count($a); $i++) {
            if ($i > 1) {
                $args .= '&';
            }
            $args .= 'args[]='.$a[$i];
        }
        $url = URL::to('/ccm/attribute_forms/tools/form/action_type/', $this->controller->getHandle(), $action);
        $url.= $args;
        return $url;
    }

    public function finishRender($contents)
    {
        print $contents;
    }

    protected function onBeforeGetContents()
    {
        @ConcreteView::element(DIRNAME_ACTION_TYPE.'/'.$view.'_header', [], MeschApp::pkgHandle());
    }

    protected function onAfterGetContents()
    {
        @ConcreteView::element(DIRNAME_ACTION_TYPE.'/'.$view.'_footer', [], MeschApp::pkgHandle());
    }

    public function field($fieldName)
    {
        return $this->controller->field($fieldName);
    }
}
