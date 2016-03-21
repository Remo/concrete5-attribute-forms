<?php
namespace Concrete\Package\AttributeForms\Controller;

use Concrete\Core\Error\Error;

trait BlockControllerExtension
{
    /** @var Error */
    protected $errors;

    protected $success_msg = array();

    /** @var \Session */
    protected $session;


    protected function bControllerExtensionInit()
    {
        $this->errors = new Error();
        $this->session = $this->app->make('session');
    }

    protected function prepareSessionSets()
    {
        if ($this->session->getFlashBag()->has('custom_message')) {
            $value = $this->session->getFlashBag()->get('custom_message');
            foreach ($value as $message) {
                $this->set($message[0], $message[1]);
            }
        } else {
            $this->set('success_msg', $this->success_msg);
        }

        if ($this->session->getFlashBag()->has('custom_error')) {
            $value = $this->session->getFlashBag()->get('custom_error');
            foreach ($value as $message) {
                $this->set($message[0], $message[1]);
            }
        } else {
            $this->set('errors', $this->errors);
        }
    }

    public function flash($key, $value)
    {
        $this->session->getFlashBag()->add('custom_message', array($key, $value));
    }

    public function flashError($key, $value)
    {
        $this->session->getFlashBag()->add('custom_error', array($key, $value));
    }

    protected function urlToAction()
    {
        $c = $this->getCollectionObject();
        if (is_object($c)) {
            $arguments = func_get_args();
            $arguments[] = $this->bID;
            array_unshift($arguments, $c);
            return call_user_func_array(array('\URL', 'to'), $arguments);
        }
    }

    protected function redirectToView()
    {
        $arguments = array($this->getCollectionObject());
        $url       = call_user_func_array(array('\URL', 'to'), $arguments);
        $this->redirect($url.'?'.http_build_query($_GET));
    }
}