<?php

namespace App;

class i18n extends Patterns\Singleton
{
    protected static $instance;
    
    protected $defaultLang;
    protected $defaultDomain;
    
    protected $debug;
    
    protected function __construct(){
        $this->defaultLang = 'ru';
        $this->defaultDomain = 'main';
        
        $this->debug = \App\Settings::i()->i18n_debug;
    }
    
    public function translate($word,$options = []){
        $lang = $options['lang'] ?? $this->defaultLang;
        $domain = $options['domain'] ?? $this->defaultDomain;
        if(!isset($this->data[$domain][$lang])){
            $this->loadDomain($domain,$lang);
        }
        
        $result = $this->data[$domain][$lang][$word] ?? $word;

        if($this->debug)
            $result = "~$result~";

        return $result;
    }
    
    public function isset($word,$domain,$lang=null){
        $lang = $lang ?? $this->defaultLang;
        return isset($this->data[$domain][$lang][$word]);
    }
    
    protected function loadDomain($domain,$lang){
        $old = isset($this->data[$domain][$lang])?$this->data[$domain][$lang]:[];
        $words = Locator::i()->locate("src/I18n/$domain.$lang.php");
        $this->data[$domain][$lang] = array_merge($old,$words);
    }
}