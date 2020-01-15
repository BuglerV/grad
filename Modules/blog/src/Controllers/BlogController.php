<?php

namespace Modules\blog\src\Controllers;

use Modules\core\src\Controllers\ProtectedAbstractController as ProtectedController;
use \Modules\core\src\Controllers\Traits\CradTraitController as CRAD;

class BlogController extends ProtectedController
{
    use CRAD;
    
    protected $prefix = 'blog_new_';
    
    protected $table = 'blog_posts';
    protected $rowsForListing = ['id','title','author'];
    
    protected $module = 'blog';
    protected $modelName = 'Post';
    
    public function __construct()
    {
        parent::__construct();
        
        \App\Output::i()->title = \App\I18n::i()->translate('blog_main_title');
    }
    
    protected function _getForm()
    {
        $form = new \App\Forms\Form('blog_new');
        
        $form->addHeader('Основные настройки');
        $form->add(new \App\Forms\TextType('blog_new_title','',[],[],['require'=>true]));
        $form->add(new \App\Forms\TextType('blog_new_author'));
        $form->add(new \App\Forms\TextType('blog_new_tags'));

        $form->addHeader('Контент');
        $form->add(new \App\Forms\CkeditorType('blog_new_preview','',[],[],['require'=>true]));
        $form->add(new \App\Forms\CkeditorType('blog_new_text'));
        
        $form->addHeader('Прикрепленные аудиозаписи');
        $form->add(new \App\Forms\ListType('blog_new_files'));
        $form->add(new \App\Forms\UploadType('blog_new_attachments','',[],[],['multiple'=>true]));
        
        $form->addHeader('Дополнительно');
        $form->add(new \App\Forms\CheckboxType('blog_new_is_opened',true));
        $form->add(new \App\Forms\DateTimeType('blog_new_pubdate','',[],[],['require'=>true]));
        
        return $form;
    }
    
    public function openAction($arguments)
    {
        if(!\App\User::i()->isLogged() OR !\App\User::i()->checkCsrf())
            exit();
        
        $post = new \Modules\blog\src\Models\Post($arguments['id']);
        if($post->isLoad()){
            \App\Output::i()->output = $post->openPost() ? 2 : 1;
        }
        else{
            \App\Output::i()->output = 0;
        }
    }
    
    protected function beforeSaving($values)
    {
        // if(strpos($_FILES['blog_new_upload']['type'],'image') !== 0)
            // unset($_FILES['blog_new_upload']);
        
        \App\Uploader::i()->saveFromPOST();
        $names = \App\Uploader::i()->getNames();

        // if(isset($names['blog_new_upload']))
            // $values['image'] = $names['blog_new_upload'][0];
        
        if(isset($names['blog_new_attachments'])){
            $files = array_merge([$values['files']],$names['blog_new_attachments']);
            $files = implode(\App\Forms\ListType::$delimiter,$files);
            $values['files'] = trim($files,'~');
        }

        return $values;
    }

}