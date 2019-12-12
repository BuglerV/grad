<?php

return [
    'blogAddNewPost' => [
        'url' => '/blog/new/?',
        'controller' => 'Modules\blog\src\Controllers\BlogController::newAction'
    ],
    'blogDeletePost' => [
        'url' => '/blog/delete/(?P<id>[0-9]+)/?',
        'ajax' => 1,
        'controller' => 'Modules\blog\src\Controllers\BlogController::deleteAction'
    ],
    'blogChangePost' => [
        'url' => '/blog/change/(?P<id>[0-9]+)/?',
        'controller' => 'Modules\blog\src\Controllers\BlogController::changeAction'
    ],
    'blogViewPost' => [
        'url' => '/blog/change/(?P<id>[0-9]+)/?',
        'controller' => 'Modules\blog\src\Controllers\BlogController::changeAction'
    ]
];