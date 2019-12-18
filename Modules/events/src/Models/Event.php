<?php

namespace Modules\events\src\Models;

class Event extends \App\Models\Model
{
    protected $table = 'events';

    protected static $loadedRows = [
        'title', 'date'
    ];
}