<?php

namespace Modules\core\windows;

use App\Window\AbstractWindow;

class FeedBack extends AbstractWindow
{
    public $isOpen = true;
    
    public function __construct($params = null)
    {
        stream_is_local();
    }
    
    public function manage()
    {
        return '123';
    }
    
    public function firstRun()
    {
    }
}