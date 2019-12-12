<?php

require '../vendor/autoload.php';

define( 'BASE'    , __DIR__ );
define( 'STORE'   , BASE . '/datastore' );
define( 'MODULES' , BASE . '/Modules' );

function dd($arg){
    echo '<pre>';
    print_r($arg);
    echo '</pre>';
}