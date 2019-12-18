<?php

namespace App\Forms;

class ListType extends AbstractType
{
    protected $type = 'text';
    protected $formType = 'list';
    
    public static $delimiter = '~~';
    
    public function getValue()
    {
        return implode(self::$delimiter,$this->value);
    }
    
    public function setValue($value = null)
    {
        $value = $value ?? \App\Request::i()->get($this->name) ?? [];
        if(!is_array($value)){
            $value = explode(self::$delimiter,$value);
        }
        
        $this->value = array_unique(array_filter($value));
    }
}