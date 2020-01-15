<?php

namespace Modules\blog;

use App\Modules\AbstractModule;

class Module extends AbstractModule
{
    public $crud = true;
    
    public function boot(){
        \App\Twig::i()->addFunction('sound_name',function($name){
            $name = basename($name);
            $name = explode('.',$name)[0];
            return $name;
        });
    }
}