<?php

namespace Modules\blog\windows;

use App\Window\AbstractWindow;

class Post extends AbstractWindow
{
    public $isOpen = true;
    public $title;
    
    public $post;
    
    public function __construct($params = null)
    {
        $id = sprintf('%d',\App\Request::i()->get('post')??$params['post']??0);

        $this->post = new \Modules\blog\src\Models\Post($id);
        
        if($this->post->isLoad()){
            $row = $this->post->getValues();
            $row['pubdate'] = date('H:i:s d-m-Y',$row['pubdate']);
            if($row['tags'])
                $row['tags'] = explode(',',$row['tags']);
        }
        $this->row = $row;

        $this->title .= 'Новость ' . $id;
    }
    
    public function manage(){
        if(!$this->post->isLoad())
            return 'No';
        
        return \App\Twig::i()->render('blog_row.twig',[
            'post' => $this->row
        ]);
    }
}