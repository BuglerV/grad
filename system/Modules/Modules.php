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
    
    public $fromDb;
    public function loadModulesFromDb($onlyModules=false){
        if($this->fromDb) return $this->fromDb;
        
        $modules = [];
        
        $query = 'SELECT * FROM '. DB_PREFIX .'modules;';
        
        $ids = [];
        foreach(\App\Db::i()->query($query,\PDO::FETCH_ASSOC) as $row){
            $name = $row['name'];;
            $modules[$name] = $row;
            $modules[$name]['windows'] = [];
            $ids[$row['id']] = $name;
        }
        
        if(!$onlyModules){
            $query = 'SELECT * FROM '. DB_PREFIX .'windows;';
            foreach(\App\Db::i()->query($query,\PDO::FETCH_ASSOC) as $row){
                $id = $ids[$row['module_id']];
                $modules[$id]['windows'][$row['name']] = $row;
            }
        }
        
        $this->fromDb = $modules;

        return $modules;
    }
    
    public function enable($do,$module,$window = null)
    {
        if(!in_array($do,['on','off'])) return false;
        $do = $do == 'on' ? 1 : 0;

        $fromFiles = $this->loadModulesFromFiles();
        if(!isset($fromFiles[$module])) return false;

        $method = $window ? 'window' : 'module';
        if($method == 'window' AND !isset($fromFiles[$module][$window]))
            return false;
        
        if($module == 'core' AND $method == 'module') return false;

        $fromDb = $this->loadModulesFromDb();
        $this->fromDb = null;
        unset(\App\Store::i()->modules);
        
        if(!isset($fromDb[$module])){
            // Запускаем новое приложение.
            $stmt = \App\Db::i()->prepare('INSERT INTO '. DB_PREFIX .'modules SET `name` = ?, `enabled` = ?');
            $enabled = $method == 'module' ? $do : 0 ;
            $stmt->execute([$module,$enabled]);
            
            $class = "Modules\\$module\\Module";
            if(class_exists($class) AND method_exists($class,'firstRun')){
                $class = new $class;
                $class->firstRun();
            }
            
            if($method == 'module') return;
            
            $id = \App\Db::i()->lastInsertId();
            if(!$id) return false;
            
            $fromDb[$module] = [
                'name' => $module,
                'id' => $id
            ];
        }
        
        if($method == 'module'){
            $stmt = \App\Db::i()->prepare('UPDATE '. DB_PREFIX .'modules SET `enabled` = ? WHERE `name` = ?;');
            $stmt->execute([$do,$module]);
            return;
        }

        if(!isset($fromDb[$module]['windows'][$window])){
            $stmt = \App\Db::i()->prepare('INSERT INTO '. DB_PREFIX .'windows SET `enabled` = ?, `name` =?, `module_id` = ?;');
            $id = $fromDb[$module]['id'];
            $stmt->execute([$do,$window,$id]);
            
            $class = "Modules\\$module\\windows\\$window";
            if(class_exists($class) AND method_exists($class,'firstRun')){
                $class = new $class;
                $class->firstRun();
            }
            return;
        }
        
        $stmt = \App\Db::i()->prepare('UPDATE '. DB_PREFIX .'windows SET `enabled` = ? WHERE `id` = ?;');
        $id = $fromDb[$module]['windows'][$window]['id'];
        $stmt->execute([$do,$id]);
    }
    
    public function loadEnabled(){
        $modules = [];
        
        $query = 'SELECT modules.name as m,windows.name as w FROM '. DB_PREFIX .'windows JOIN '. DB_PREFIX .'modules ON windows.module_id = modules.id WHERE modules.enabled = 1 AND windows.enabled = 1;';
        
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
    
    public $fromFiles;
    public function loadModulesFromFiles()
    {
        if($this->fromFiles) return $this->fromFiles;
        
        $res = [];
        $dir = new \DirectoryIterator(BASE . '/Modules/');
        foreach($dir as $oneDir){
            if($oneDir->isDot() OR !$oneDir->isDir()) continue;
            $res[$oneDir->getFilename()] = [];
            $file = new \DirectoryIterator($oneDir->getPathname() . '/windows/');
            foreach($file as $oneFile){
                if(!$oneFile->isFile()) continue;
                $fileName = $oneFile->getBasename('.php');
                $res[$oneDir->getFilename()][$fileName] = $fileName;
            }
        }
        
        $this->fromFiles = $res;
        
        return $res;
    }
    
    public function getModuleVars($module = null)
    {
        $modules = $this->loadModulesFromDb();
        $realModules = $this->loadModulesFromFiles();

        foreach($realModules as $name => $windows){
            if(!isset($modules[$name])){
                $modules[$name] = [
                    'enabled' => 0,
                    'name' => $name
                ];
            }
            foreach($windows as $window){
                if(!isset($modules[$name]['windows'][$window])){
                    $modules[$name]['windows'][$window] = [
                        'enabled' => 0,
                        'name' => $window,
                        'module_id' => $modules[$name]['id'] ?? 'null'
                    ];
                }
            }
        }
        
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