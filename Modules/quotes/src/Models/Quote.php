<?php

namespace Modules\quotes\src\Models;

class Quote extends \App\Models\Model
{
    protected $table = 'quotes';
    
    protected static $loadedRows = [
        'author', 'quote'
    ];
}