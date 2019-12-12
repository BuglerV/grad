<?php

namespace Modules\blog\src\Models;

class Post extends \App\Models\Model
{
    protected $table = 'blog_posts';
    
    protected $loadedRows = [
        'title', 'author', 'text', 'preview', 'image', 'tags', 'is_opened', 'pubdate'
    ];
    
    protected function setter_pubdate($value)
    {
        $this->pubdate = $value ?: time();
    }
    
    protected function setter_tags($values)
    {
        $tags = [];
        if($values){
            $values = explode(',',$values);
            foreach($values as $value){
                $value = str_replace(' ','',mb_convert_case($value,MB_CASE_TITLE));
                $tags[] = $value;
            }
            $tags = implode(',',array_filter(array_unique($tags)));
        }
        $this->tags = $tags ?: '';
    }
}