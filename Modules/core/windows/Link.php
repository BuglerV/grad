<?php

namespace Modules\core\windows;

use App\Window\AbstractWindow;

class Link extends AbstractWindow
{
    public function __construct($params = null)
    {
    }
    
    public function manage(){
        $links = '<div class="window_body">' . \App\Settings::i()->window_links . '</div>';
        return $links ? '<h>'. \App\I18n::i()->translate('link_main_title',['domain'=>'link']) .'</h>' . $links : '';
    }
}