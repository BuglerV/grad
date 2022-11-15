<?php

namespace Modules\blog;

use App\Modules\AbstractModule;

class Module extends AbstractModule
{
    public $crud = true;
    
    public function boot(){
        \App\Twig::i()->addFunction('sound_name',function($name){
            if(stream_is_local($name)){
                $name = implode('.',explode('.',trim(basename($name)),-3));
            }
            else{
                $name = explode('.',basename($name))[0];
            }
            return $name;
        });
    }
}