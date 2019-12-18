<?php

namespace App\Modules;

class Modules extends \App\Patterns\Singleton
{
    /**
     * Object.
     */
    protected static $instance;
    
    /**
     * Each exists module full name array.
     */
    protected $names = [];
    
    protected function __construct(){
        $modules = \App\Store::i()->modules ?? $this->loadEnabled();

        foreach($modules as $name => $vars){
            if(!is_dir(MODULES . "/$name")){
                unset($modules[$name]);
                \App\Logger::i()->log(sprintf('Попытка загрузить несуществующий модуль "%s".',$name),'warning',['Module']);
            }
            else{
                $this->names[] = MODULES . "/$name";
            }
        }
        $this->data = $modules;
    }
    
    public function getSettings($module){
        $fileName = MODULES . "/$module/Settings.php";
        if(is_file($fileName)){
            include $fileName;
            return $form;
        }
        return 'Для этого модуля не предусмотрены настройки.';
    }
    
    public function loadModulesFromDb($onlyModules=false){
        $modules = [];
        
        $query = 'SELECT * FROM modules;';
        foreach(\App\Db::i()->query($query,\PDO::FETCH_ASSOC) as $row){
            $id = $row['id']; unset($row['id']);
            $modules[$id] = $row;
            $modules[$id]['windows'] = [];
        }
        
        if(!$onlyModules){
            $query = 'SELECT * FROM windows;';
            foreach(\App\Db::i()->query($query,\PDO::FETCH_ASSOC) as $row){
                $id = $row['module_id']; unset($row['module_id']);
                $modules[$id]['windows'][] = $row;
            }
        }

        return $modules;
    }
    
    public function loadEnabled(){
        $modules = [];
        
        $query = 'SELECT modules.name as m,windows.name as w FROM windows JOIN modules ON windows.module_id = modules.id WHERE modules.enabled = 1 AND windows.enabled = 1;';
        
        foreach(\App\Db::i()->query($query,\PDO::FETCH_ASSOC) as $row){
            if(!isset($modules[$row['m']]))
                $modules[$row['m']] = [];
            
            $modules[$row['m']][$row['w']] = 1;
        }
        
        if(!isset($modules['core']))
            $modules['core'] = [];

        \App\Store::i()->modules = $modules;
        
        return $modules;
    }
    
    public function windowIsEnable($module,$window){
        return $this->$module AND isset($this->$module[$window]);
    }
    
    /**
     * Return module's full name.
     */
    public function getNames(){
        return $this->names;
    }
    
    /**
     * Start exists module.
     *
     * Return void.
     */
    public function boot()
    {
        //if(!\App\Request::i()->isXmlHttpRequest()){
            foreach($this->getNames() as $name){
                $name = basename($name);
                $class = "Modules\\$name\\Module";
                if(file_exists(BASE . '/' . str_replace('\\','/',$class) . '.php')){
                    $module = new $class;
                    $module->boot();
                }
            }
        //}
        return true;
    }
}