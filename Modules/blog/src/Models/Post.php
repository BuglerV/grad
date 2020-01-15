<?php

namespace Modules\blog\src\Models;

class Post extends \App\Models\Model
{
    protected $table = 'blog_posts';

    protected static $loadedRows = [
        'title', 'author', 'text', 'preview', 'files', 'tags', 'is_opened', 'pubdate'
    ];
    
    protected function setter_pubdate($value)
    {
        $this->data['pubdate'] = $value ?: time();
    }
    
    public function openPost(){
        if($this->isLoad()){
            $this->is_opened = $this->is_opened ? 0 : 1;
            $this->save();
            
            return $this->is_opened;
        }
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
        $this->data['tags'] = $tags ?: '';
    }
}