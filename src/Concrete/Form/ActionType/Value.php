<?php

namespace Concrete\Package\AttributeForms\Form\ActionType;

class Value
{
    protected $ID;
    protected $bID;
    protected $actionName;
    protected $actionType;
    protected $actionData;
    protected $executionOrder;
    
    private $data;

    public function __construct($row = array())
    {
        $this->setPropertiesFromArray($row);
        if(empty($this->ID)){
            $this->ID = uniqid('atf_');
        }
    }
    
    function getID()
    {
        return $this->ID;
    }

    function getBID()
    {
        return $this->bID;
    }

    function getActionName()
    {
        return $this->actionName;
    }

    function getActionType()
    {
        return $this->actionType;
    }

    function getActionData()
    {
        return $this->actionData;
    }

    function setID($ID)
    {
        $this->ID = $ID;
    }

    function setBID($bID)
    {
        $this->bID = $bID;
    }

    function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }

    function setActionType($actionType)
    {
        $this->actionType = $actionType;
    }

    function setActionData($actionData)
    {
        if(is_array($actionData)){
            $this->data = $actionData;
            $this->actionData = serialize($actionData);
        }else{
            $this->actionData = $actionData;
            if (!empty($this->actionData)) {
                $this->data = @unserialize($this->actionData);
            }else{
                $this->data = array();
            }
        }
    }

    public function getDataArray()
    {
        return $this->data;
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
    
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'get') {
            $field = lcfirst(substr($name, 3));
            if (isset($this->data[$field])) {
                return $this->data[$field];
            }
        }
    }
}