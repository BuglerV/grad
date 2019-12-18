<?php

namespace Modules\events\src\Controllers;

use Modules\core\src\Controllers\ProtectedAbstractController as ProtectedController;
use \Modules\core\src\Controllers\Traits\CradTraitController as CRAD;

class EventController extends ProtectedController
{
    use CRAD;
    
    protected $prefix = 'event_';
    protected $textName = 'событие';
    
    protected $table = 'events';
    protected $rowsForListing = ['*'];
    
    protected $module = 'events';
    protected $modelName = 'Event';
    
    public function __construct()
    {
        parent::__construct();
        
        \App\Output::i()->title = 'Анонсы';
    }
    
    protected function _getForm()
    {
        $form = new \App\Forms\Form('event_form');
        $form->add(new \App\Forms\TextType('event_title','',[],[],['max_length'=>'255','require'=>true]));
        $form->add(new \App\Forms\DateTimeType('event_date'),'',[],[],['require'=>true]);
        
        return $form;
    }

}