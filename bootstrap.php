<?php

require '../vendor/autoload.php';

define( 'BASE'    , __DIR__ );
define( 'BASE_PUBLIC', BASE . '/public' );
define( 'STORE'   , BASE . '/datastore' );
define( 'MODULES' , BASE . '/Modules' );

define( 'MAIN_PATH' , '/' );

function dd($arg){
    $debug = debug_backtrace()[0];
    echo '<pre>';
    echo $debug['file'] . ' - ' . $debug['line'] . '<br>';
    print_r($arg);
    echo '</pre>';
}