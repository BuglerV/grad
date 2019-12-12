<?php

return [
    // 'pagePath' => [
        // 'url' => '/page(?P<Method>[a-z]+)/?',
        // 'method' => ['GET'],
        // 'controller' => 'Modules\core\src\Controllers\PageController::pageAction'
    // ],
    'windowPath' => [
        'url' => '/window/(?P<Module>[a-z]+)/(?P<Window>[a-z]+)/?',
        'controller' => 'Modules\core\src\Controllers\WindowController::indexAction'
    ]
];