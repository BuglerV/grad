<?php

namespace Modules\core\src\Controllers;

class AdminController extends ProtectedAbstractController
{
    public function indexAction(){
        \App\Output::i()->output = '<h>Территория администрации</h>ываыввы';
        \App\Output::i()->title = 'Admin';
    }
    
    public function settingAction($arguments){
        \App\Output::i()->title = 'Настройки';

        $res = '<h>Настройки модуля '. $arguments['module'] .'</h>';

        $res .=  \App\Modules\Modules::i()->getSettings($arguments['module']);
        
        \App\Output::i()->output = $res;
    }
}