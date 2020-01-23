<?php

namespace Modules\core\src\Controllers;

use App\I18n;

class PageController
{
    public function indexAction(){
        \App\Output::i()->output = \App\Twig::i()->render('base/base.twig');
    }
    
    public function __call($method,$arguments){
        $method = 'page/' . mb_strtolower($arguments[0]['Method']);
        $columns = \App\Store::i()->$method;

        \App\Output::i()->output = \App\Twig::i()->render('core_page_base.twig',[
            'columns' => $columns
        ]);
        
        \App\Output::i()->title = I18n::i()->translate('Title');
    }
}