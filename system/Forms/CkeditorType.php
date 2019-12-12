<?php

namespace App\Forms;

class CkeditorType extends AbstractType
{
    protected static $is_load_js = false;
    
    public function __construct($name=null,$value=null,$classes=[],$attributes=[],$options=[]){
        parent::__construct($name,$value,$classes,$attributes,$options);
        
        if(!self::$is_load_js){
            \App\Output::i()->ckeditor = true;
            self::$is_load_js = true;
        }
    }
    
    protected $type = 'ckeditor';
    protected $formType = 'textarea';
}