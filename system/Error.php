<?php

namespace App;

class Error
{
    public function register()
    {
        set_error_handler([$this,'errorHandler']);
        set_exception_handler([$this,'exceptionHandler']);
        register_shutdown_function([$this,'fatalErrorHandler']);
    }
    
    protected function logError($errno,$error,$file,$line)
    {
        $error = str_replace(["\r","\n","\0"],' ',$error);
        $logText = "[{$_SERVER['REMOTE_ADDR']}] [{$_SERVER['REQUEST_METHOD']}] [{$_SERVER['REQUEST_URI']}] [{$errno}] [{$file}|{$line}] [{$error}]\n";
        
        \App\Logger::i()->log($logText,'warning',['Error']);
    }
    
    public function errorHandler($errno,$error,$errfile,$errline)
    {
        $this->logError($errno,$error,$errfile,$errline);
        return true;
    }
    
    public function exceptionHandler($e)
    {
        if(get_class($e) != 'Exception')
            throw($e);
        
        $this->logError($e->getCode(),$e->getMessage(),$e->getFilename(),$e->getLine());
        return true;
    }
    
    public function fatalErrorHandler()
    {
        if($e = error_get_last()){
            $this->logError($e['type'],$e['message'],$e['file'],$e['line']);
            ob_end_clean();
            
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'){
                echo 'Проблемы на сервере. Перезагрузите страницу или попробуйте позже.';
                return;
            }
            echo file_get_contents(BASE_PUBLIC . '/404.html');
        }
    }
}