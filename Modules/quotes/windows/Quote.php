<?php

namespace Modules\quotes\windows;

use App\Window\AbstractWindow;

class Quote extends AbstractWindow
{
    public $isOpen = true;
    public $title = 'Цитата';
    
    public function manage()
    {
        $where = ''; $bind = [];
        if($name = \App\Request::i()->get('author')){
            $where = ' WHERE author = ?';
            $bind = [$name];
        }
        
        $query = 'SELECT count(*) FROM quotes'. $where .';';
        $stmt = \App\Db::i()->prepare($query);
        $stmt->execute($bind);
        if(!$count = $stmt->fetch()[0]) return;

        $query = sprintf('SELECT * FROM quotes%s LIMIT %d,1;',$where,rand(0,$count-1));
        
        $stmt = \App\Db::i()->prepare($query);
        $stmt->execute($bind);
        $row = $stmt->fetch();

        return \App\Twig::i()->render('quote.twig',[
            'quote' => $row
        ]);
    }
}