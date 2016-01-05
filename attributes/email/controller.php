<?php

namespace Concrete\Package\AttributeForms\Attribute\Email;

use Concrete\Core\Attribute\DefaultController;
use Core;

class Controller extends DefaultController
{
    protected $searchIndexFieldDefinition = array('type' => 'text', 'options' => array('length' => 4294967295, 'default' => null, 'notnull' => false));

    public function form()
    {
        if (is_object($this->attributeValue)) {
            $value = Core::make('helper/text')->email($this->getAttributeValue()->getValue());
        }
        print Core::make('helper/form')->email($this->field('value'), $value);
    }

    public function composer()
    {
        if (is_object($this->attributeValue)) {
            $value = Core::make('helper/text')->email($this->getAttributeValue()->getValue());
        }
        print Core::make('helper/form')->email($this->field('value'), $value, array('class' => 'col-xs-5'));
    }

    public function validateForm($data)
    {
        $valid = false;
        if($data['value'] != ''){
            $val = Core::make('helper/validation/strings');
            if (!$val->email($data['value'])) {
                $e = Core::make('helper/validation/error');
                $e->add(t('Invalid email address provided.'));
                $valid = $e;
            }else{
                $valid = true;
            }
        }
        return $valid;
    }
}