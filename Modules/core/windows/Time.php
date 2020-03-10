<?php

namespace Modules\core\windows;

use App\Window\AbstractWindow;

class Time extends AbstractWindow
{
    public function __construct($params = null)
    {
    }
    
    public function manage(){
        $time = \App\DateTime::i()->set()->getDT('G:i:s');

        return \App\Twig::i()->render('core_timer.twig',[
            'time' => $time
        ]);
    }
}