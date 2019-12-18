<?php

namespace Modules\events\windows;

use App\Window\AbstractWindow;

class Event extends AbstractWindow
{
    public $isOpen = false;
    public $title = 'Анонсы';
    
    public function manage()
    {
        $today = \App\DateTime::i()->getToday();
        $tomorrow = \App\DateTime::i()->getTomorrow();
        
        $query = 'SELECT * FROM events WHERE date >= '. $today .' AND date < '. $tomorrow .' ORDER BY date;';
        $stmt = \App\Db::i()->query($query,\PDO::FETCH_ASSOC);
        $todays = $stmt->fetchAll();
        
        $query = 'SELECT * FROM events WHERE date >= '. $tomorrow .' ORDER BY date LIMIT 5;';
        $stmt = \App\Db::i()->query($query,\PDO::FETCH_ASSOC);
        $wills = $stmt->fetchAll();

        return \App\Twig::i()->render('events.twig',[
            'tomorrows' => $wills,
            'todays' => $todays
        ]);
    }
}