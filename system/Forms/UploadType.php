<?php

namespace App\Forms;

class UploadType extends AbstractType
{
    protected $type = 'upload';
    protected $formType = 'upload';
    
    protected static $uploaded = false;
    
    public function modifyFromForm($form){
        if(!static::$uploaded){
            static::$uploaded = true;
            $form->addUpload();
        }
    }
}