<?php

namespace App;

class DateTime extends Patterns\Singleton
{
    protected static $instance;
    
    protected $currentTime;
    
    protected $dateTS;
    
    protected function __construct(){
        $this->currentTime = time();
    }
    
    public function getTS()
    {
        if(!$this->ts)
            $this->ts = $this->currentTime;
        
        return $this->ts;
    }
    public function setTS($value)
    {
        $this->ts = $value ? +$value : $this->currentTime;
    }
    public function getDT()
    {
        if(!$this->ts)
            $this->ts = $this->currentTime;
        
        $date = new \DateTime();
        $date->setTimestamp($this->ts);
        return $date->format('Y-m-d\TH:i');
    }
    public function setDT($value)
    {
        $date = new \DateTime($value);
        $this->ts = $date->getTimestamp();
    }
    
    public function getToday()
    {
        $date = new \DateTime();
        $date->setTime(0,0,0,0);
        return $date->getTimestamp();
    }
    public function getTomorrow()
    {
        $date = new \DateTime();
        $date->setTime(0,0,0,0);
        $date->modify('+1 day');
        return $date->getTimestamp();
    }
    
    public function set($value)
    {
        $datetime = preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/',$value);
        if($datetime)
            return $this->setDT($value);

        $this->setTS($value);
    }
}