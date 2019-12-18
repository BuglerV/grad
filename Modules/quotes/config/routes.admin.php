<?php

return [
    'quoteAdd' => [
        'url' => '/quotes/new/?',
        'controller' => 'Modules\quotes\src\Controllers\QuoteController::newAction'
    ],
    'quoteDelete' => [
        'url' => '/quotes/delete/(?P<id>[0-9]+)/?',
        'ajax' => 1,
        'controller' => 'Modules\quotes\src\Controllers\QuoteController::deleteAction'
    ],
    'quoteChange' => [
        'url' => '/quotes/change/(?P<id>[0-9]+)/?',
        'controller' => 'Modules\quotes\src\Controllers\QuoteController::changeAction'
    ],
    'quoteListing' => [
        'url' => '/quotes/list/?',
        'controller' => 'Modules\quotes\src\Controllers\QuoteController::listAction'
    ]
];