<?php

define( 'DEV', true );

require '../vendor/autoload.php';

define( 'BASE'    , __DIR__ );
define( 'BASE_PUBLIC', BASE . '/public' );
define( 'STORE'   , BASE . '/datastore' );
define( 'MODULES' , BASE . '/Modules' );

define( 'DB_PREFIX' , '' );

define( 'MAIN_PATH' , '/' );

if(DEV){
    error_reporting(-1);
    function dd($arg){
        $debug = debug_backtrace()[0];
        $res = '<div class="bugler_dumper" style="background-color:black;color:white;padding:10px;">';
        $res .= '<div style="color:yellow;" class="bugler_dumper_title" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display == \'none\' ? \'block\' : \'none\';">';
        
        $res .= $debug['file'] . ' - ' . $debug['line'];
        
        $res .= '</div>';
        $res .= '<pre style="display:none;">';
        $res .= print_r($arg,1);
        $res .= '</pre></div>';
        
        echo $res;
    }

}
else{
    error_reporting(0);
    (new \App\Error())->register();
}