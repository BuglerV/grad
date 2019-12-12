<?php

namespace App;

class i18n extends Patterns\Singleton
{
    protected static $instance;
    
    protected $defaultLang;
    protected $defaultDomain;
    
    protected function __construct(){
        $this->defaultLang = 'ru';
        $this->defaultDomain = 'main';
    }
    
    public function translate($word,$options = []){
        $lang = $options['lang'] ?? $this->defaultLang;
        $domain = $options['domain'] ?? $this->defaultDomain;
        if(!isset($this->data[$domain][$lang])){
            $this->loadDomain($domain,$lang);
        }
        
        return $this->data[$domain][$lang][$word] ?? $word;
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