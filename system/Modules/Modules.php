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
    
    public function getSettings($module,$onlyCheck=false){
        $fileName = MODULES . "/$module/Settings.php";
        if(is_file($fileName)){
            if($onlyCheck)
                return true;
            include $fileName;
            return $form;
        }
        return false;
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
    
    public function getModuleVars($module = null)
    {
        $modules = $this->loadModulesFromDb();
        
        foreach($modules as $key => $value){
            $modules[$key]['settings'] = $this->getSettings($value['name'],true);
            
            $class = "Modules\\{$value['name']}\\Module";
            if(file_exists(BASE . '/' . str_replace('\\','/',$class) . '.php')){
                $module = new $class;
                $modules[$key]['crud'] = $module->crud;
            }
            
            $modules[$key]['I18n'] = [];
            $dir = new \DirectoryIterator(BASE . "/Modules/{$value['name']}/src/I18n/");
            foreach($dir as $oneDir){
                if($oneDir->isDot() OR !$oneDir->isFile())
                    continue;
                
                if($oneDir->getExtension() == 'php'){
                    $name = explode('.',$oneDir->getFilename());
                    $modules[$key]['I18n'][] = [
                        'domain' => $name[0],
                        'lang' => $name[1]
                    ];
                }
            }
        }
        
        return $modules;
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