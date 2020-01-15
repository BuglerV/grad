<?php

namespace Modules\core\src\Controllers\Traits;

trait CradTraitController
{
    protected $model;
    
    public function listAction()
    {
        $dbname = \App\Db::getDbName();
        $table = $this->table;
        $rows = implode(', ',$this->rowsForListing);
        
        $query = "SELECT TABLE_ROWS FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = '$table';";
        $stmt = \App\Db::i()->query($query);

        $count = $stmt->fetch(\PDO::FETCH_NUM)[0];
        $max = 25;
        
        $pages = ceil($count / $max);
        $page = sprintf('%d',\App\Request::i()->get('page') ?? 1);
        $page = $page < 1 ? 1 : ($page > $pages ? $pages : $page);
        
        // Достаю посты...
        $query = sprintf('SELECT %s FROM %s ORDER BY id DESC LIMIT %d,%d;',$rows,$table,($page - 1) * $max,$max);
        $stmt = \App\Db::i()->query($query);
        
        $result = $stmt->fetchAll();
        
        $paginator = \App\Twig::i()->render('core_paginator.twig',[
            'module' => $this->module,
            'page' => $page,
            'pages' => $pages,
            'jsMethod' => 'core_paginator'
        ]);
        
        \App\Output::i()->output = \App\Twig::i()->render($this->module.'_listing.twig',[
            'rows' => $result,
            'module' => $this->module,
            'paginator' => $paginator
        ]);
    }
    
    public function newAction()
    {
        $form = $this->_getForm();
        
        if($form->isSubmitted() AND $form->isValid()){
            $values = [];
            foreach($form->values() as $key => $value){
                $key = str_replace($this->prefix,'',$key);
                $values[$key] = $value;
            }
            
            $modelClass = "\\Modules\\{$this->module}\\src\\Models\\{$this->modelName}";
            
            $this->model = new $modelClass;
            
            if(method_exists($this,'beforeNew'))
                $this->beforeNew();
            
            if(method_exists($this,'beforeSaving'))
                $values = $this->beforeSaving($values);
            
            $this->model->setValues($values);
            $this->model->save();
            
            header('Location: /admin/' . $this->module . '/list');
        }

        \App\Output::i()->output = '<a class="admin_button" href="/admin/'. $this->module .'/list">'. \App\I18n::i()->translate('crud_all') .'</a><h>Добавить '. \App\I18n::i()->translate($this->module . '_main_whom') .'</h>' . $form;
    }
    
    public function deleteAction($arguments)
    {
        if(!\App\User::i()->isLogged() OR !\App\User::i()->checkCsrf())
            exit();
        
        $modelClass = "\\Modules\\{$this->module}\\src\\Models\\{$this->modelName}";

        $this->model = new $modelClass($arguments['id']);
        if($this->model->isLoad()){
            \App\Output::i()->output = $this->model->delete() ? 2 : 1;
        }
        else{
            \App\Output::i()->output = 3;
        }
    }
    
    public function changeAction($arguments)
    {
        $modelClass = "\\Modules\\{$this->module}\\src\\Models\\{$this->modelName}";
        $this->model = new $modelClass($arguments['id']);

        if(!$this->model->isLoad()){ return; }
        
        $form = $this->_getForm();
        
        if($form->isSubmitted())
        {
            if($form->isValid()){
                $values = [];
                foreach($form->values() as $key => $value){
                    $key = str_replace($this->prefix,'',$key);
                    $values[$key] = $value;
                }

                if(method_exists($this,'beforeSaving'))
                    $values = $this->beforeSaving($values);

                $this->model->setValues($values);
                $this->model->save();
                // Здесь нужно вывести сообщение о сохранении...
            }
        }

        $values = [];
        foreach($this->model->getValues() as $key => $value){
            $values[$this->prefix . $key] = $value;
        }
        
        if(method_exists($this,'beforeOutput'))
            $values = $this->beforeOutput($values);

        $form->setValues($values);
        
        \App\Output::i()->output = '<a class="admin_button" href="/admin/'. $this->module .'/list">'. \App\I18n::i()->translate('crud_all') .'</a><h>Редактируем '. \App\I18n::i()->translate($this->module . '_main_whom') .'</h>' . $form;
    }
}