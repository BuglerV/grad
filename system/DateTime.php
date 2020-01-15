<?php

namespace App;

class DateTime extends Patterns\Singleton
{
    protected static $instance;
    
    protected $currentTime;
    protected $ts;
    protected $format = 'Y-m-d H:i';
    
    protected function __construct($time=null){
        $this->currentTime = time();
        $this->ts = new \DateTime($time ?? null);
    }
    
    public function format($format=null)
    {
        return $this->ts->format($format ?? $this->format);
    }
    
    public function setFormat($format)
    {
        $this->format = $format;
    }
    
    public function setToday($day = null)
    {
        $this->ts = new \DateTime();
        $this->ts->setTime(0,0,0,0);
        return $this;
    }
    
    public function modify($modify)
    {
        $this->ts->modify($modify);
        return $this;
    }
    
    
    public function setDate($day=null,$month=null,$year=null)
    {
        $year = $year ?? $this->ts->format('Y');
        $month = $month ?? $this->ts->format('m');
        $day = $day ?? $this->ts->format('d');

        $this->ts->setDate($year,$month,$day);
        return $this;
    }
    public function setTime($hour,$min,$sec = 0,$micro = 0)
    {
        $this->ts->setTime($hour,$min,$sec,$micro);
        return $this;
    }
    
    public function getTS()
    {
        return $this->ts->getTimestamp();
    }
    public function setTS($value = null)
    {
        $this->ts->setTimestamp($value ? +$value : $this->currentTime);
    }
    
    
    public function getDT($format = null)
    {
        return $this->ts->format($format ?? $this->format);
    }
    public function setDT($value)
    {
        $this->ts = new \DateTime($value);
    }

    
    public function set($value=null)
    {
       // $datetime = preg_match('/^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}(:\d{2})?$/',$value);
       // if($datetime)
            $this->setDT($value);
            return $this;

        //$this->setTS($value);
    }
}