<?php

namespace App\Forms;

class DateTimeType extends AbstractType
{
    protected $type = 'datetime-local';
    protected $formType = 'text';
    
    public function __construct($name,$value=null,$classes=[],$attributes=[],$options=[])
    {
        $value = $value ?: time();
        parent::__construct($name,$value,$classes,$attributes,$options);
    }
    
    public function getValue()
    {
        \App\DateTime::i()->set($this->value);
        return \App\DateTime::i()->getTS();
    }
    
    public function setValue($value = null)
    {
        if(null===$value){
            $name = $this->name;
            $value = \App\Request::i()->get($name) ?? time();
        }

        \App\DateTime::i()->set($value);
        $this->value = \App\DateTime::i()->getDT();
    }
}