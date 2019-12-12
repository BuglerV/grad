<?php

namespace App;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request
{
    protected static $instance;
    
    public static function i($request = null){
        if(static::$instance === null){
            static::setRequest($request);
        }
        return static::$instance;
    }
    
    public static function setRequest($request){
        static::$instance = $request ?? SymfonyRequest::createFromGlobals();
    }
}