<?php
namespace Concrete\Package\AttributeForms\Form\ActionType;

use Concrete\Package\AttributeForms\Entity\AttributeForm;
use Concrete\Package\AttributeForms\Form\ActionType\Value as ActionTypeValue;

abstract class AbstractController extends \Concrete\Core\Controller\AbstractController
{
    protected $helpers = array('form');
    protected $fieldPrefix = 'actForm';
    protected $value;
    
    public function __construct()
    {
        parent::__construct();
        $this->setApplication(\Core::getFacadeApplication());
        $this->set('controller', $this);
        $this->setValue(new ActionTypeValue());
    }

    abstract function getPackageHandle();

    public function setValue(ActionTypeValue $value)
    {
        $this->value = $value;
    }

    /**
     * @return ActionTypeValue
     */
    public function getValue()
    {
        return $this->value;
    }

    public function form()
    {
        
    }

    private function getCurrentDir()
    {
        $rc = new \ReflectionClass(get_class($this));
        return dirname($rc->getFileName());
    }

    public function getHandle()
    {
        return basename($this->getCurrentDir());
    }

    public function validateForm(array $data, $actionID)
    {
        return true;
    }

    public function field($field)
    {
        return "{$this->fieldPrefix}[{$this->value->getID()}][$field]";
    }

    public function fieldValue($field, $default = null)
    {
        $formData = \Request::post($this->fieldPrefix);
        if (is_array($formData[$this->value->getID()])) {
            return $formData[$this->value->getID()][$field];
        }

        $data = $this->value->getDataArray();
        if (isset($data[$field])) {
            return $data[$field];
        }

        return $default;
    }

    public function getParsedData(array $args, $actionID)
    {
        return serialize($args[$this->fieldPrefix][$actionID]);
    }

    public abstract function execute(AttributeForm $form, array $data = array());
}