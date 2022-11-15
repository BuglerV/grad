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
    
    'adminI18n' => [
        'url' => '/i18n/(?P<module>[a-z]+)/(?P<domain>[a-z]+)/(?P<lang>[a-z]+)/?',
        'controller' => 'Modules\core\src\Controllers\AdminController::i18nAction'
    ],
    
    
    // Модификация страницы...
    'pageModify' => [
        'url' => '/pageModify/?',
        'controller' => 'Modules\core\src\Controllers\PageModifyController::indexAction'
    ],
    'pageModifySave' => [
        'url' => '/pageModify/save/?',
        'ajax' => 'true',
        'controller' => 'Modules\core\src\Controllers\PageModifyController::saveAction'
    ],
];