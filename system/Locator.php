<?php

namespace App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;

class Locator extends Patterns\Singleton
{
    protected static $instance;
    
    protected $locator;
    
    protected function __construct(){
        $modules = \App\Modules\Modules::i()->getNames();
        $this->setLocator(array_merge($modules,[BASE]));
        //$this->setLocator(\App\Modules\Modules::i()->getNames());
    }
    
    public function setLocator($dirs){
        $this->locator = new FileLocator($dirs);
    }
    
    public function locate($name,$first=false,$module=null){
        try{
            $files = $this->locator->locate($name,$module,$module?true:$first);
        }
        catch(FileLocatorFileNotFoundException $e){
            return [];
        }
        
        $configs = [];
        foreach($files as $file){
            $file = include($file);
            if(is_array($file))
                $configs = array_merge($configs,$file);
        }
        return $configs;
    }
    
}