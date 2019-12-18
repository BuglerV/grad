<?php

return [
    'adminLoginPath' => [
        'url' => '/login/?',
        'controller' => 'Modules\core\src\Controllers\AuthorisationController::loginAction'
    ],
    'adminLogoutPath' => [
        'url' => '/logout/?',
        'controller' => 'Modules\core\src\Controllers\AuthorisationController::logoutAction'
    ],
    'adminSettingPath' => [
        'url' => '/setting/(?P<module>[a-z]+)/?',
        'controller' => 'Modules\core\src\Controllers\AdminController::settingAction'
    ],
];