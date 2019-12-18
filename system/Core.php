<?php

namespace App;

class Core extends Patterns\Singleton
{
    protected static $instance;
    
    protected function __construct($env = 'public'){
        // Only first...
        Env::i()->wall = $env;

        Kernel::i()->addListener(Kernel::DISPATCH,'\\App\\Router','dispatch');
        Kernel::i()->addListener(Kernel::ROUTE,'\\App\\Router','controller');
        
        Kernel::i()->addListener(Kernel::START,'\\App\\Modules\\Modules','boot');
    }
    
    public function run(){
        Kernel::i()->handle();
        
        echo Output::i()->getOutput();
    }
}