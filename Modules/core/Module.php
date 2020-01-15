<?php

namespace Modules\core;

use App\Modules\AbstractModule;

class Module extends AbstractModule
{
    public function boot(){
        \App\Twig::i()->addFunction('window',function($name,$params){
            $path = explode('\\',$name);
            if(!\App\Modules\Modules::i()->windowIsEnable($path[1],$path[3]))
                return '';
            
            $class = new $name($params);

            return $class->manage();
        });
        
        \App\Twig::i()->addFunction('translate',function($word,$domain=null,$lang=null){
            return \App\I18n::i()->translate($word,['domain'=>$domain,'lang'=>$lang]);
        });
        
        \App\Twig::i()->addFunction('setting',function($name){
            return \App\Settings::i()->$name;
        });
        
        \App\Twig::i()->addFunction('time',function($ts,$format){
            \App\DateTime::i()->set($ts);
            return \App\DateTime::i()->format($format);
        });
        
        \App\Twig::i()->addFunction('user',function($name){
            if(!\App\User::i())
                return '';
            return \App\User::i()->$name;
        });
    }
}