<?php

namespace Modules\core\src\Controllers;

abstract class ProtectedAbstractController
{
    public function __construct(){
        if(\App\User::i()->isLogged())
            return;
        
        \App\Session::i()->from = '/admin' . \App\Request::i()->getPathInfo();
        
        header('Location: /admin/login/');
        exit();
    }
}