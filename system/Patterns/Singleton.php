<?php

namespace App\Patterns;

abstract class Singleton
{
    protected $data = [];
    
    private function __construct(){}
    
    public static function i(...$args){
        if(static::$instance === null){
            $class = get_called_class();
            static::$instance = new $class(...$args);
        }
        return static::$instance;
    }
    
    public function __get($var){
        return isset($this->data[$var]) ? $this->data[$var] : null;
    }
    
    public function getAll(){
        return $this->data;
    }
    
    public function delete(){
        static::$instance = null;
    }
    
}