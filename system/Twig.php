<?php

namespace App;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\TwigFunction;

use App\Modules\Modules;

class Twig extends Patterns\Singleton
{
    protected static $instance;
    
    protected const TEMPLATE_PATH = "/src/Templates";
    
    protected $templates;
    
    protected function __construct(){
        $this->setDirs(Modules::i()->getNames());
    }
    
    public function setDirs($modules = [],$reload = true){
        $dirs = [];
        //$dirs = [BASE . self::TEMPLATE_PATH];
        foreach($modules as $name){
            $dirs[] = $name . self::TEMPLATE_PATH;
        }
        $loader = new FilesystemLoader($dirs);
        $this->data['env'] = new Environment($loader,[
            'cache' => STORE . '/twig',
            'auto_reload' => $reload
        ]);
    }
    
    public function addFunction($name,$params){
        $this->env->addFunction(new TwigFunction($name,$params));
    }
    
    public function render($name,array $args = []){
        if(!isset($this->templates[$name])){
            try{
                $this->templates[$name] = $this->env->load($name);
            }
            catch(LoaderError $e){
                \App\Logger::i()->log(sprintf('Попытка загрузить несуществующий вид "%s".',$name),'warning',['Twig']);
                return '';
            }
        }  
        return $this->templates[$name]->render($args);
    }
}