<?php

namespace Modules\core\src\Controllers;

use Modules\core\src\Controllers\ProtectedAbstractController as ProtectedController;

class RadioController extends ProtectedController
{
    protected $path = STORE . '/logs/radio_connect/';
    
    public function indexAction()
    {
        $date = date('Y-m-d',time());
        $filename = $this->path . $date . '.log';
        $file = file($filename);
        dd($file);
    }
}