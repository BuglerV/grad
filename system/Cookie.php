<?php

namespace App;

class Cookie extends Patterns\Singleton
{
    protected static $instance;
    
    protected function __construct(){
        $this->data = $_COOKIE;
    }
    
    public function __set($key,$value){
        $this->data[$key] = $value;
        setcookie($key,$value,time()+(3600*24*356),'/');
    }
    
    public function __unset($key){
        unset($this->data[$key]);
        setcookie($key,false,time()-3600,'/');
    }
    
}