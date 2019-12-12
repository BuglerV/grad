<?php

namespace Modules\blog\windows;

use App\Window\AbstractWindow;

class Foo extends AbstractWindow
{
    public function manage(){
        return "<h>Предстоящие события</h>сюда тянет последние сообщения с форума.";
    }
}