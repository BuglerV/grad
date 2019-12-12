<?php

namespace Modules\core;

use App\Modules\ModuleInterface;

class Module implements ModuleInterface
{
    public function boot(){
        \App\Twig::i()->addFunction('window',function($name,$params){
            $path = explode('\\',$name);
            if(!\App\Modules\Modules::i()->windowIsEnable($path[1],$path[3]))
                return '';
            
            $class = new $name($params);

            return $class->manage();
        });
        
        \App\Twig::i()->addFunction('setting',function($name){
            return \App\Settings::i()->$name;
        });
    }
}