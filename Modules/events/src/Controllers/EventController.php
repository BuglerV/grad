<?php

namespace Modules\events\src\Controllers;

use Modules\core\src\Controllers\ProtectedAbstractController as ProtectedController;
use \Modules\core\src\Controllers\Traits\CradTraitController as CRAD;

class EventController extends ProtectedController
{
    use CRAD;
    
    protected $prefix = 'event_';
    
    protected $table = 'events';
    protected $rowsForListing = ['*'];
    
    protected $module = 'events';
    protected $modelName = 'Event';
    
    public function __construct()
    {
        parent::__construct();
        
        \App\Output::i()->title = \App\I18n::i()->translate('events_main_title');
    }
    
    protected function _getForm()
    {
        $form = new \App\Forms\Form('event_form');
        $form->add(new \App\Forms\TextType('event_title','',[],[],['require'=>true]));
        $form->add(new \App\Forms\CkeditorType('event_text'));
        $form->add(new \App\Forms\CkeditorType('event_look'));
        $form->add(new \App\Forms\DateTimeType('event_date','',[],[],['type'=>'date','require'=>true]));
        $form->add(new \App\Forms\DateTimeType('event_time','',[],[],['type'=>'time','require'=>true]));

        return $form;
    }
    
    protected function beforeNew()
    {
        unset(\App\Store::i()->events);
    }

}