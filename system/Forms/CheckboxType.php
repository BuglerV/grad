<?php

namespace App\Forms;

class CheckboxType extends AbstractType
{
    protected $type = 'checkbox';
    protected $formType = 'checkbox';
    
    public function setValue($value=null){
        if(null===$value){
            $name = $this->name;
            $value = \App\Request::i()->get($name) ?? false;
        }
        $this->value = (bool)$value;
    }
}