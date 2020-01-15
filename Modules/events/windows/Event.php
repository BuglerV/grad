<?php

namespace Modules\events\windows;

use App\Window\AbstractWindow;

class Event extends AbstractWindow
{
    public $isOpen = true;
    public $title = 'Анонсы';
    
    protected $today;
    
    public function __construct()
    {
        $this->today = \App\DateTime::i()->setToday()->format('Y-m-d');
    }
    
    public function manage()
    {
        $day = null;
        if(\App\Request::i()->isXmlHttpRequest() AND $day = \App\Request::i()->get('event_day')){
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$day) OR $day <= '2010-00-00' OR $day >= '2120-00-00')
                return '';
        }

        $days = $this->_getOneDay($day)->fetchAll();
        
        if($day){
            return \App\Twig::i()->render('events_wills.twig',[
                'tomorrows' => $days
            ]);
        }
        
        $wills = $this->_getPeriodFrom($day)->fetchAll();
        
        if(!isset(\App\Store::i()->events)){
            $this->_updateStore();
        }
        $storedEvents = \App\Store::i()->events;
        
        $calendar = \App\Twig::i()->render('events_calendar.twig',[
            'days' => \App\DateTime::i()->setToday()->format('t'),
            'void' => \App\DateTime::i()->setDate(1)->format('N') - 1,
            'stored' => $storedEvents,
            'today' => $this->today,
            'month' => \App\DateTime::i()->format('Y-m')
        ]);
        
        \App\Output::i()->jsVars[] = [
            'name' => 'today',
            'value' => $this->today
        ];
        
        \App\Output::i()->jsVars[] = [
            'name' => 'future_events',
            'value' => join('~',array_keys($storedEvents))
        ];
        
        \App\Output::i()->js[] = 'js/events.js';

        return \App\Twig::i()->render('events.twig',[
            'tomorrows' => $wills,
            'todays' => $days,
            'calendar' => $calendar
        ]);
    }
    
    protected function _updateStore()
    {
        $query = 'SELECT date,COUNT(*) AS num FROM events WHERE date >= "' . $this->today . '" GROUP BY date;';
        $stmt = \App\Db::i()->query($query,\PDO::FETCH_ASSOC);
        
        $values = [];
        while($row = $stmt->fetch()){
            $values[$row['date']] = $row['num'];
        }
        \App\Store::i()->events = $values;
    }
    
    protected function _getPeriodFrom($day = null)
    {
        $day = $day ?: $this->today;
        
        $query = 'SELECT * FROM events WHERE date > "'. $day .'" ORDER BY date,time LIMIT 5;';
        return \App\Db::i()->query($query,\PDO::FETCH_ASSOC);
    }
    
    protected function _getOneDay($day = null)
    {
        $day = $day ?: $this->today;
        
        $query = 'SELECT * FROM events WHERE date = "'. $day .'" ORDER BY time;';
        return \App\Db::i()->query($query,\PDO::FETCH_ASSOC);
    }
}