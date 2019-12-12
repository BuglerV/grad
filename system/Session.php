<?php

namespace App;

class Session extends Patterns\Singleton
{
    protected static $instance;
    
    protected function __construct(){
        session_start();
        $this->data = $_SESSION;
    }
    
    public function __set($key,$value){
        $this->data[$key] = $value;
        $_SESSION[$key] = $value;
    }
    
    public function __unset($key){
        unset($this->data[$key]);
        unset($_SESSION[$key]);
    }
    
}