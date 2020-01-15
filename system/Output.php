<?php

namespace App;

class Output extends Patterns\Singleton
{
    protected static $instance;
    
    public $output;
    
    public $isAjax;
    
    public $css = [];
    public $js = ['js/jquery-3.2.1.min.js'];
    public $meta = [];
    
    public $jsVars = [];
    
    public $ckeditor = false;
    
    public $title;
    
    protected function __construct()
    {
        $this->css[] = 'css/main.css?t=' . microtime(true);
        $this->js[] = 'js/main.js?t=' . microtime(true);
        $this->isAjax = \App\Request::i()->isXmlHttpRequest();
    }
    
    protected function getTitle(){
        $name = Settings::i()->siteName;
        return $this->title ? $this->title . ' | ' . $name : $name;
    }
    
    public function addJsVar($name,$value)
    {
        $this->jsVars[] = [
            'name' => $name,
            'value' => $value
        ];
    }
    
    public function getOutput(){
        if($this->isAjax)
            return $this->output;
        
        if($this->ckeditor)
            $this->js[] = 'ckeditor/ckeditor.js?t=' . microtime(true);
        
        if(\App\User::i()->role == 'admin')
            $this->addJsVar('csrf',\App\User::i()->createCsrfToken());
        
        return Twig::i()->render('base/'. Env::i()->wall .'.twig',[
            'body' => $this->output,
            'css' => $this->css,
            'js' => $this->js,
            'meta' => $this->meta,
            'title' => $this->getTitle(),
            'pathInfo' => MAIN_PATH,
            'jsVars' => $this->jsVars
        ]);
    }
}