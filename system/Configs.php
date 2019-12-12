<?php

namespace App;

class Configs extends Patterns\Singleton
{
    protected static $instance;
    
    protected function __construct(){
        $this->data = Locator::i()->locate('config/globals.php');
    }
}