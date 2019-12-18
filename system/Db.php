<?php

namespace App;

class Db extends Patterns\Singleton
{
    protected static $instance;
    
    protected static $config;
    
    public static function getDbName(){
        return self::$config['dbname'];
    }
    
    public static function i(...$args){
        if(null === static::$instance){
            $config = include(BASE . '/config/database.php');
            if(!$config){
                Logger::i()->log('Отсутствуют настройки подключения к базе данных.','warning',['Db']);
                die; //////////////////////////////////////////////
            }
            self::$config = $config;
            
            static::$instance = new \PDO("mysql:dbname={$config['dbname']};host={$config['host']};port={$config['port']}",$config['user'],$config['pass']);
        }
        return static::$instance;
    }
}