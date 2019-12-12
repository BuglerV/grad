<?php

namespace App;

class Kernel extends Patterns\Singleton
{
    /**
     * Object.
     */
    protected static $instance;
    
    /**
     * Event names.
     */
    public const START = 'START';
    public const DISPATCH = 'DISPATCH';
    public const ROUTE = 'ROUTE';
    public const FINISH = 'FINISH';
    public const ERROR = 'ERROR';
    
    /**
     * Add new listener.
     *
     * Param 'event' - name of event to listen.
     * Param 'class' - listener's class.
     * Param 'method' - listener's method.
     * Param 'weight' - weight of listener.
     *
     * Return void.
     */
    public function addListener($event,$class,$method,$weight=100)
    {
        $this->data[$event][$weight][] = [
            'class' => $class,
            'method' => $method
        ];
    }
    
    /**
     * Start event.
     *
     * Param 'event' - name of needed event.
     *
     * Return void.
     */
    public function eventStart($event)
    {
        if(isset($this->data[$event]) AND $weights = $this->data[$event]){
            ksort($weights);
            foreach($weights as $weight){
                foreach($weight as $listener){
                    $class = $listener['class'];
                    $method = $listener['method'];

                    if(strpos($class,'\\App\\') === 0){
                        $class::i()->$method();
                    }
                    else{
                        $class = new $class;
                        if(!$class->$method())
                            break 2;
                    }
                }
            }
        }
    }
    
    /**
     * Start event dispatcher.
     *
     * Return void.
     */
    public function handle()
    {
        $this->eventStart(Kernel::START);
        $this->eventStart(Kernel::DISPATCH);
        $this->eventStart(Kernel::ROUTE);
        $this->eventStart(Kernel::FINISH);
    }
}