<?php

namespace Modules\core\windows;

use App\Window\AbstractWindow;

class Base extends AbstractWindow
{
    public $isOpen = true;
    public $title = 'Модули';
    
    public function manage()
    {
        $res = '<h>Модули</h>';
        return $res;
    }
}