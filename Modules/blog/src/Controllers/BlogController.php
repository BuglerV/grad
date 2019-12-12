<?php

namespace Modules\blog\src\Controllers;

use Modules\core\src\Controllers\ProtectedAbstractController as ProtectedController;

class BlogController extends ProtectedController
{
    
    public function __construct()
    {
        parent::__construct();
        
        \App\Output::i()->title = 'Новости';
    }
    
    protected function _getPostFrom()
    {
        $form = new \App\Forms\Form('blog_new');
        $form->add(new \App\Forms\TextType('blog_new_title','',[],[],['require'=>true]));
        $form->add(new \App\Forms\TextType('blog_new_author'));
        $form->add(new \App\Forms\TextType('blog_new_tags'));
        $form->add(new \App\Forms\TextType('blog_new_image'));
        $form->add(new \App\Forms\UploadType('blog_new_upload'));
        $form->add(new \App\Forms\CkeditorType('blog_new_preview','',[],[],['max_length'=>'200','require'=>true]));
        $form->add(new \App\Forms\CkeditorType('blog_new_text'));
        $form->add(new \App\Forms\CheckboxType('blog_new_is_opened',true));
        $form->add(new \App\Forms\DateTimeType('blog_new_pubdate',true));
        
        return $form;
    }
    
    public function newAction()
    {
        $form = $this->_getPostFrom();
        
        if($form->isSubmitted() AND $form->isValid()){
            $values = [];
            foreach($form->values() as $key => $value){
                $key = str_replace('blog_new_','',$key);
                $values[$key] = $value;
            }
            
            $post = new \Modules\blog\src\Models\Post();
            $post->setValues($values);
            $post->save();
            // Здесь нужно вывести сообщение о сохранении...
        }

        \App\Output::i()->output = '<h>Добавить новость</h>' . $form;
    }
    
    public function changeAction($arguments)
    {
        $post = new \Modules\blog\src\Models\Post($arguments['id']);

        if(!$post->isLoad()){ return; }
        
        $form = $this->_getPostFrom();
        
        if($form->isSubmitted())
        {
            if($form->isValid()){
                $values = [];
                foreach($form->values() as $key => $value){
                    $key = str_replace('blog_new_','',$key);
                    $values[$key] = $value;
                }

                $post->setValues($values);
                $post->save();
                // Здесь нужно вывести сообщение о сохранении...
            }
        }
        else
        {
            $values = [];
            foreach($post->getValues() as $key => $value){
                $values['blog_new_' . $key] = $value;
            }
            
            $form->setValues($values);
        }
        
        \App\Output::i()->output = '<h>Редактируем новость</h>' . $form;
    }
    
    public function deleteAction($arguments)
    {
        if(!\App\User::i()->isLogged() OR !\App\User::i()->checkCsrf())
            exit();

        $post = new \Modules\blog\src\Models\Post($arguments['id']);
        if($post->isLoad()){
            \App\Output::i()->output = $post->delete() ? 0 : 1;
        }
        else{
            \App\Output::i()->output = 2;
        }
    }

}