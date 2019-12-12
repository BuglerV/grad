<?php

namespace App\Forms;

class NumberType extends AbstractType
{
    protected $type = 'number';
    protected $formType = 'text';
    
    protected function verifyValue(){
        $options = $this->options;
        $value = +$this->value;

        if(!is_integer($value)){
            $this->error = 'Введено не число';
            return;
        }
        if(isset($options['min']) AND $value < $options['min']){
            $this->error = 'Слишком маленькое число';
            return;
        }
        if(isset($options['max']) AND $value > $options['max']){
            $this->error = 'Слишком большое число';
            return;
        }
    }
}