<?php

namespace App\Forms;

class DateTimeType extends AbstractType
{
    protected $type = 'datetime-local';
    protected $formType = 'text';
    protected $format = 'Y-m-d\TH:i';
    
    public function __construct($name,$value=null,$classes=[],$attributes=[],$options=[])
    {
        if(isset($options['type'])){
            $this->type = $options['type'];
            unset($options['type']);
            $this->format = $this->type == 'time' ? 'H:i' : 'Y-m-d';
        }
        
        parent::__construct($name,$value,$classes,$attributes,$options);
    }
    
    public function setValue($value = null)
    {
        if(null===$value){
            $name = $this->name;
            $value = \App\Request::i()->get($name) ?? null;
        }
        
        \App\DateTime::i()->set($value);
        
        $this->value = \App\DateTime::i()->format($this->format);
    }
}