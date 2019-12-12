<?php

namespace App\Forms;

use App\I18n;

abstract class AbstractType implements TypeInterface
{
    protected $name;
    protected $value;
    protected $classes;
    protected $attributes;
    protected $options;
    protected $error;
    
    public function __construct($name=null,$value=null,$classes=[],$attributes=[],$options=[])
    {
        $this->name = $name;
        $this->value = $value;
        $this->classes = $classes;
        $this->attributes = $attributes;
        $this->options = $options;
    }
    
    public function __toString(){
        $info = I18n::i()->isset($this->getName().'_info','form') ?
           I18n::i()->translate($this->getName().'_info',['domain'=>'form']) : '';

        return \App\Twig::i()->render('form/form_' . $this->formType . '.twig',[
            'title' => I18n::i()->translate($this->getName(),['domain'=>'form']),
            'name' => $this->name,
            'value' => $this->value,
            'classes' => implode(' ',$this->classes),
            'attributes' => implode(' ',$this->attributes),
            'type' => $this->type,
            'info' => $info,
            'error' => $this->error
        ]);
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function isError(){
        return $this->error;
    }
    
    protected function verifyValue(){
        if(isset($this->options['require']) AND $this->options['require'] AND !$this->value){
            $this->error = 'Поле обязательно для заполнения.';
            return false;
        }
        if(isset($this->options['min_length']) AND mb_strlen($this->value)<$this->options['min_length']){
            $this->error = 'Слишком короткий текст. Минимальная длинна - ' . $this->options['min_length'] . ' символов.';;
            return false;
        }
        if(isset($this->options['max_length']) AND mb_strlen($this->value)>$this->options['max_length']){
            $this->error = 'Слишком длинный текст. Максимальная длинна - ' . $this->options['max_length'] . ' символов.';
            return false;
        }
        
        return true;
    }
    
    public function getValue(){
        return $this->value;
    }
    
    public function setValue($value=null){
        if(null===$value){
            $value = \App\Request::i()->get($this->name) ?? '';
        }

        $this->value = $value;
        $this->verifyValue();
    }
}