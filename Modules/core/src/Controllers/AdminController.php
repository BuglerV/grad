<?php

namespace Modules\core\src\Controllers;

class AdminController extends ProtectedAbstractController
{
    public function indexAction(){
        if($do = \App\Request::i()->get('do') AND \App\User::i()->checkCsrf()){
            $module = \App\Request::i()->get('module');
            $window = \App\Request::i()->get('window');
            \App\Modules\Modules::i()->enable($do,$module,$window);
        }
        
        $modules = \App\Modules\Modules::i()->getModuleVars();
        \App\Output::i()->output = \App\Twig::i()->render('admin_main.twig',[
            'modules' => $modules
        ]);
        \App\Output::i()->title = 'Admin';
    }
    
    public function settingAction($arguments){
        \App\Output::i()->title = 'Настройки';

        $res = '<h>Настройки модуля '. $arguments['module'] .'</h>';

        $res .=  \App\Modules\Modules::i()->getSettings($arguments['module']);
        
        \App\Output::i()->output = $res;
    }
    
    public function i18nAction($args){
        $fileName = MODULES . "/{$args['module']}/src/I18n/{$args['domain']}.{$args['lang']}.php";
        
        if(!is_file($fileName))
            return;
        
        $words = include($fileName);
        
        $form = new \App\Forms\Form('coreI18n');
        \App\Env::i()->formNotTranslate = true;
        
        foreach($words as $key => $value){
            $form->add(new \App\Forms\TextType($key,$value));
        }
        
        if($form->isSubmitted() AND $form->isValid()){
            $fileData = "<?php\n\nreturn ".var_export($form->values(),true).";\n\n//Stored " . date('Y-m-d H:i:s',time());
            
            file_put_contents($fileName,$fileData);
        }
        
        \App\Output::i()->output = "<h>Перевод {$args['module']}-{$args['domain']}-{$args['lang']}</h>$form";
    }
}