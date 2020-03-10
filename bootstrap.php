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
        echo '<pre>';
        echo $debug['file'] . ' - ' . $debug['line'] . '<br>';
        print_r($arg);
        echo '</pre>';
    }

}
else{
    error_reporting(0);
    (new \App\Error())->register();
}