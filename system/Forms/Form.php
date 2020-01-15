<?php

namespace App\Forms;

class Form
{
    private $id;
    private $attributes;
    private $options;
    
    private $submit;
    private $isLoad = false;
    
    private $valid = true;
    
    private $uploads = false;
    
    private $hiddens = [];
    private $elements = [];
    
    public function __construct($id=null,$attributes=null,$options=null)
    {
        $this->id = $id;
        $this->submit = ($this->id ? $this->id . '_' : '') . 'submit';
        $this->attributes = $attributes;
        $this->options = $options;
    }
    
    public function add($element)
    {
        if(is_subclass_of($element,'\App\Forms\TypeInterface')){
            $this->elements[$element->getName()] = $element;
        }
        
        $element->modifyFromForm($this);
    }
    
    public function addLine()
    {
        $this->elements['el'.count($this->elements)] = new Line;
    }
    
    public function addHeader($text = null)
    {
        if(!$text) return;
        
        $this->elements['el'.count($this->elements)] = new HeaderText($text);
    }
    
    public function addCustom(...$args)
    {
        $this->elements['el'.count($this->elements)] = new Custom(...$args);
    }

    public function save()
    {
        foreach($this->elements as $element){
            if(is_subclass_of($element,'App\Forms\TypeInterface')){
                $name = $element->getName();
                \App\Settings::i()->$name = $element->getValue();
            }
        }
    }
    
    public function addUpload()
    {
        $this->uploads = true;
    }

    public function valuesFromRequest()
    {
        foreach($this->elements as $element){
            if(!is_subclass_of($element,'App\Forms\TypeInterface'))
                continue;
            
            $element->setValue();
            
            if($element->isError())
                $this->valid = false;
        }
        $this->isLoad = true;
    }
    
    public function values()
    {
        if(!$this->isLoad) $this->valuesFromRequest();
        
        $values = [];
        foreach($this->elements as $element){
            if(is_subclass_of($element,'App\Forms\TypeInterface'))
                $values[$element->getName()] = $element->getValue();
        }
        return $values;
    }
    
    public function setValue($element,$value)
    {
        if(isset($this->elements[$element]) AND
           is_subclass_of($this->elements[$element],'App\Forms\TypeInterface')){
               $this->elements[$element]->setValue($value);
           }
    }
    
    public function setValues($values)
    {
        foreach($this->elements as $element){
            if(is_subclass_of($element,'App\Forms\TypeInterface'))
                if(isset($values[$element->getName()]))
                    $element->setValue($values[$element->getName()]);
        }
    }
    
    public function isValid($checkCsrf = true)
    {
        if(!$this->isLoad) $this->valuesFromRequest();
        if($checkCsrf AND !\App\User::i()->checkCsrf())
            return false;
        return $this->valid;
    }
    
    public function isSubmitted()
    {
        return \App\Request::i()->get($this->submit);
    }
    
    public function __toString()
    {
        $elems = [];
        
        if(!isset($this->options['csrf']) OR $this->options['csrf']==true)
            $this->hiddens[] = [
                'name' => 'csrf',
                'value' => \App\User::i()->createCsrfToken()
            ];

        foreach($this->elements as $element){
            $elems[] = $element;
        }
        
        $elems[] = new ButtonType(($this->id ? $this->id . '_' : '') .'submit',\App\I18n::i()->translate('submit',['domain'=>'form'],false));
        
        $res = \App\Twig::i()->render('form/form_global.twig',[
            'id' => $this->id,
            'method' => $this->options['method'] ?? 'POST',
            'action' => $this->options['action'] ?? '',
            'elements' => $elems,
            'hiddens' => $this->hiddens,
            'uploads' => $this->uploads
        ]);

        return $res;
    }
}