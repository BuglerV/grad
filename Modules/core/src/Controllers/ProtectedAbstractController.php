<?php

namespace Modules\core\src\Controllers;

abstract class ProtectedAbstractController
{
    public function __construct(){
        if(\App\User::i()->isLogged())
            return;
        
        if(\App\Request::i()->isXmlHttpRequest())
            exit();
        
        \App\Session::i()->from = '/admin' . \App\Request::i()->getPathInfo();
        
        header('Location: /admin/login/');
        exit();
    }
}