<?php

namespace Modules\core\src\Controllers;

use App\I18n;

class WindowController
{
    public function indexAction($arguments){
        $module = $arguments['Module'];
        $window = $arguments['Window'];
        
        if(!\App\Modules\Modules::i()->windowIsEnable($module,$window))
            return '';
        
        $moduleFullName = "\\Modules\\$module\\windows\\$window";
        $moduleClass = new $moduleFullName;
        
        if(!$moduleClass->isOpen)
            return '';
        
        \App\Output::i()->title = $moduleClass->title ?? '';
        \App\Output::i()->output = $moduleClass->manage();
    }
}