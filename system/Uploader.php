<?php

namespace App;

class Uploader extends Patterns\Singleton
{
    protected static $instance;
    
    protected $last_names = [];
    
    public function getNames()
    {
        return $this->last_names;
    }
    
    public function saveFromPOST()
    {
        if(!$_FILES)
            return;
        
        foreach($_FILES as $key => $file){
            if(is_array($file['name'])){
                $count = count($file['name']);
                for($i = 0; $i < $count; $i++){
                    $array = array_column($file,$i);
                    $array = array_combine(['name','type','tmp_name','error','size'],$array);
                    $this->realSave($array,$key);
                }
            }
            else{
                $this->realSave($file,$key);
            }
        }
    }
    
    private function realSave($file,$key)
    {
        if($file['error'] !== 0)
            return;
        
        list($type,$ext) = explode('/',$file['type']);
        
        if(!in_array($type,['image','audio']))
            return;
        
        $fh = fopen($file['tmp_name'],'r');
        $content = md5(fread($fh,1024));
        fclose($fh);
        
        $dir = $type . '/' . date('mY',time());
        if(!is_dir($dir)) mkdir($dir);
        
        $name = $dir . '/' . basename($file['name']) . ".$content.$ext";

        if(move_uploaded_file($file['tmp_name'],$name)){
            $this->last_names[$key][] = '/' . $name;
        }
    }
}