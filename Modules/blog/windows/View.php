<?php

namespace Modules\blog\windows;

use App\Window\AbstractWindow;
use App\Db;

class View extends AbstractWindow
{
    public $isOpen = true;
    
    public $title = 'Блог';
    
    public function manage(){
        $where = [];
        $bind = [];

        // Проверяю наличие тегов...
        if($name = \App\Request::i()->get('name')){
            $where[] = $name=='author' ? '`author` = ?' : '`tags` LIKE ?';
            $tag = \App\Request::i()->get('tag');
            $bind[] = $name == 'author' ? $tag : '%'.$tag.'%';
        }
        
        // Проверяю наличие поиска...
        elseif($row = \App\Request::i()->get('row')){
            $search = \App\Request::i()->get('search');
            foreach(\Modules\blog\src\Models\Post::$searchedRows as $one => $v){
                if($one === $row){
                    $where[] = "`$one` LIKE ?";
                    $bind[] = '%'.$search.'%';
                    
                    if($row == 'text'){
                        $where[count($where)-1] .= ' OR `preview` LIKE ?';
                        $bind[] = '%'.$search.'%';
                    }
                    break;
                }
            }
            $blog_search = [
                'search' => $search,
                'row' => $row
            ];
            $tag = 'Поиск';
        }
        
        $time = time();
        if(!\App\User::i()->isLogged() OR \App\User::i()->role != 'admin'){
            // Только открытые...
            $where[] = 'is_opened = 1';
            // Для отображения только новых постов...
            $where[] = 'pubdate < ' . $time;
        }
        
        $where = $where ? ' WHERE ' . implode(' AND ',$where) : '' ;
        $query = sprintf('SELECT count(*) FROM blog_posts%s;',$where);
        
        $stmt = Db::i()->prepare($query);
        $stmt->execute($bind);
        $post_count = $stmt->fetch()[0];
        
        // Для пагинации...
        $max = \App\Settings::i()->blog_max_posts;
        $pages = ceil($post_count / $max);
        $page = sprintf('%d',\App\Request::i()->get('page') ?? 1);
        $page = $page < 1 ? 1 : ($page > $pages ? $pages : $page);

        // Достаю посты...
        $query = sprintf('SELECT * FROM blog_posts%s ORDER BY pubdate DESC LIMIT %d,%d;',$where,($page - 1) * $max,$max);
        $stmt = Db::i()->prepare($query);
        $stmt->execute($bind);

        // Собираю посты для простого отображения...
        $posts = [];
        foreach($stmt->fetchAll() as $row){
            if($row['pubdate'] > $time)
                $row['after'] = true;

            if($row['files']){
                $files = array_filter(explode(\App\Forms\ListType::$delimiter,$row['files']));
                $result = [];
                foreach($files as $file){
                   // if(strpos(mime_content_type(BASE_PUBLIC . $file),'image')===0)
                        $result[] = $file;
                }
                $row['files'] = $result;
            }
            
            if($row['tags'])
                $row['tags'] = explode(',',$row['tags']);
            $posts[] = $row;
        }
        
        // Получаю пагинатор...
        $paginator = \App\Twig::i()->render('core_paginator.twig',[
            'page' => $page,
            'pages' => $pages,
            'jsMethod' => 'blog_page'
        ]);
        
        // Опции...
        $options = [];
        foreach(\Modules\blog\src\Models\Post::$searchedRows as $key => $value){
            $options[] = ['name'=>$key,'read'=>$value];
        }

        // Отображаю...
        return \App\Twig::i()->render('blog_main.twig',[
            'posts' => $posts,
            'paginator' => $paginator ?? '',
            'tag' => $tag ?? '',
            'blog_search' => $blog_search ?? '',
            'options' => $options
        ]);
    }
}