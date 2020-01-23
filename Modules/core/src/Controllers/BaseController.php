<?php

namespace Modules\core\src\Controllers;

class BaseController
{
    public function indexAction(){
        $method = 'page/main';
        $columns = \App\Store::i()->$method;
        
        $first = array_shift($columns);
        
        \App\Output::i()->output = \App\Twig::i()->render('core_page_base.twig',[
            'columns' => $columns,
            'first' => $first
        ]);
        \App\Output::i()->title = 'Главная';
    }
    
    public function page404Action(){
        \App\Output::i()->title = 'Ошибка';
        \App\Output::i()->output = __METHOD__;
    }
}