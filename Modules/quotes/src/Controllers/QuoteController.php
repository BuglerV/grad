<?php

namespace Modules\quotes\src\Controllers;

use Modules\core\src\Controllers\ProtectedAbstractController as ProtectedController;
use \Modules\core\src\Controllers\Traits\CradTraitController as CRAD;

class QuoteController extends ProtectedController
{
    use CRAD;
    
    protected $prefix = 'quote_';
    protected $textName = 'цитату';
    
    protected $table = 'quotes';
    protected $rowsForListing = ['*'];
    
    protected $module = 'quotes';
    protected $modelName = 'Quote';
    
    public function __construct()
    {
        parent::__construct();
        
        \App\Output::i()->title = 'Цитаты';
    }
    
    protected function _getForm()
    {
        $form = new \App\Forms\Form('quote_form');
        $form->add(new \App\Forms\TextType('quote_author'));
        $form->add(new \App\Forms\CkeditorType('quote_quote','',[],[],['max_length'=>'200','require'=>true]));
        
        return $form;
    }
}