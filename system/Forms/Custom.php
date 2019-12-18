<?php

namespace App\Forms;

class Custom
{
    private $title;
    private $body;
    private $info;
    private $error;
    
    public function __construct($body='',$title='',$info='',$error='')
    {
        $this->title = $title;
        $this->body = $body;
        $this->info = $info;
        $this->error = $error;
    }
    
    public function __toString()
    {
        return \App\Twig::i()->render('form/form_container.twig',[
            'title' => $this->title,
            'body' => $this->body,
            'info' => $this->info,
            'error' => $this->error
        ]);
    }
}