<?php

namespace Modules\core\src\Controllers;

class AuthorisationController
{
    public function logoutAction(){
        unset(\App\Cookie::i()->username);
        unset(\App\Cookie::i()->password);
        
        header('Location: /');
        exit();
    }
    
    public function loginAction(){
        $res = '<h>Авторизация</h>';

        $form = new \App\Forms\Form('main');
        $form->add(new \App\Forms\TextType('username'));
        $form->add(new \App\Forms\PasswordType('password'));

        if($form->isSubmitted()){
            $form->valuesFromRequest();
            $values = $form->values();

            \App\Cookie::i()->username = $values['username']; 
            \App\Cookie::i()->password = \App\User::i()->createPassword($values['password']);
            
            $from = \App\Session::i()->from ?? '/admin/';
            
            unset(\App\Session::i()->from);
     
            header('Location: ' . $from);
            exit();
        }

        $res .= $form;

        \App\Output::i()->title = 'Авторизация';
        \App\Output::i()->output = $res;
    }
}