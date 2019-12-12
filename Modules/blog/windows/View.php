<?php

namespace Modules\blog\windows;

use App\Window\AbstractWindow;
use App\Db;

class View extends AbstractWindow
{
    public $isOpen = true;
    
    public $title = 'Блог';
    
    public function manage(){
        $time = time();

        if($name = \App\Request::i()->get('name')){
            $where = $name=='author' ? ' AND `author` = ?' : ' AND `tags` LIKE ?';
            $tag = \App\Request::i()->get('tag');
            $bind = [$name == 'author' ? $tag : '%'.$tag.'%'];
        }
        $where = $where ?? '';
        $bind = $bind ?? [];
        
        $query = 'SELECT count(*) FROM blog_posts WHERE is_opened = 1 AND pubdate < '. $time . $where . ';';
        $stmt = Db::i()->prepare($query);

        $stmt->execute($bind);
        $post_count = $stmt->fetch()[0];
        
        $max = \App\Settings::i()->blog_max_posts;
        $pages = ceil($post_count / $max);

        $page = sprintf('%d',\App\Request::i()->get('page') ?? 1);
        $page = $page < 1 ? 1 : ($page > $pages ? $pages : $page);

        $query = sprintf('SELECT * FROM blog_posts WHERE is_opened = 1 AND pubdate < '. $time . $where . ' ORDER BY id DESC LIMIT %d,%d;',($page - 1) * $max,$max);
        $stmt = Db::i()->prepare($query);
        $stmt->execute($bind);
        
        $posts = [];
        foreach($stmt->fetchAll() as $row){
            $row['pubdate'] = date('H:i:s d-m-Y',$row['pubdate']);
            if($row['tags'])
                $row['tags'] = explode(',',$row['tags']);
            $posts[] = $row;
        }
        
        if(true OR $pages > 1){
            $buttons = [];
            if($page > 1){
                $buttons[] = ['Первая' => 1];
                $buttons[] = ['Пред.' => $page - 1];
            }
            
            $paginator = \App\Twig::i()->render('blog_paginator.twig',[
                'page' => $page,
                'pages' => $pages
            ]);
        }
        
        return \App\Twig::i()->render('blog_main_template.twig',[
            'posts' => $posts,
            'paginator' => $paginator ?? '',
            'tag' => $tag ?? ''
        ]);
    }
}