<?php

namespace App\Forms;

class HeaderText
{
    protected $text;
    
    public function __construct($text)
    {
        $this->text = $text;
    }
    
    public function __toString()
    {
        return '<div class="blog_header">' . $this->text . '</div>';
    }
}