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
    
    protected function login($values)
    {
        if(isset($values['username']) AND preg_match('/^[a-zA-Z][a-zA-Z0-9_-]{2,}$/',$values['username'])){
            $member = \App\User::i()->getUserFromDb($values['username']);
            if($member){
                if($member['error_count'] >= 3)
                    return 'Превышено количество неверных попыток входа. Аккаунт заблокирован. Обратитесь к администратору.';
                
                if(\App\User::i()->checkPassword($member['password'],$values['password'])){
                    \App\Cookie::i()->password = $values['password'];
                    \App\Cookie::i()->username = $member['name'];
                    
                    \App\User::i()->setErrorCount($member['id'],0);
                            
                    $from = \App\Session::i()->from ?? '/';
                    
                    unset(\App\Session::i()->from);
             
                    header('Location: ' . $from);
                    exit();
                }
                
                \App\User::i()->setErrorCount($member['id'],$member['error_count']+1);
            }
        }
        return 'Не вырные логин или пароль.';
    }
    
    public function loginAction(){
        if(\App\User::i()->isLogged()){
            header('Location: /');
            exit();
        }
        
        $res = '<h>Авторизация</h>';

        $form = new \App\Forms\Form('main');
        $form->add(new \App\Forms\TextType('username'));
        $form->add(new \App\Forms\PasswordType('password'));

        if($form->isSubmitted()){
            $form->valuesFromRequest();
            $values = $form->values();
            
            $errors = $this->login($values);
        }

        $res .= $form . ($errors ?? '');

        \App\Output::i()->title = 'Авторизация';
        \App\Output::i()->output = $res;
    }
}