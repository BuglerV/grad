<?php

return [
    'eventAdd' => [
        'url' => '/events/new/?',
        'controller' => 'Modules\events\src\Controllers\EventController::newAction'
    ],
    'eventDelete' => [
        'url' => '/events/delete/(?P<id>[0-9]+)/?',
        'ajax' => 1,
        'controller' => 'Modules\events\src\Controllers\EventController::deleteAction'
    ],
    'eventChange' => [
        'url' => '/events/change/(?P<id>[0-9]+)/?',
        'controller' => 'Modules\events\src\Controllers\EventController::changeAction'
    ],
    'eventList' => [
        'url' => '/events/list/?',
        'controller' => 'Modules\events\src\Controllers\EventController::listAction'
    ]
];