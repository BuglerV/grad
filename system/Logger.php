<?php

namespace App;

use Monolog\Logger as BaseLogger;
use Monolog\Handler\StreamHandler;

class Logger extends Patterns\Singleton
{
    protected static $instance;
    
    protected $log;
    
    protected function __construct(){
        $this->log = new BaseLogger('GlobalLogger');
        $this->log->pushHandler(new StreamHandler(STORE . "/logs/base.log"));
        $this->log->pushHandler(new StreamHandler(STORE . "/logs/error.log",BaseLogger::WARNING,false));
    }
    
    public function log($message,$level='Info',$context=[]){
        $method = 'add' . ucfirst($level);
        if(method_exists($this->log,$method))
            $this->log->$method($message,$context);
    }
}