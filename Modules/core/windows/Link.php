<?php

namespace Modules\core\windows;

use App\Window\AbstractWindow;

class Link extends AbstractWindow
{
    public function __construct($params = null)
    {
    }
    
    public function manage(){
        $links = \App\Settings::i()->window_links;
        return $links ? '<h>'. \App\I18n::i()->translate('link_main_title',['domain'=>'link']) .'</h>' . $links : '';
    }
}