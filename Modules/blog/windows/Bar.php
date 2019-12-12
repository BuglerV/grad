<?php

namespace Modules\blog\windows;

use App\Window\AbstractWindow;

class Bar extends AbstractWindow
{
    public function manage(){
        return "<h>Цитаты</h>рандомная цитата..";
    }
}