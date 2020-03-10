<?php

define('BG',true);

require '../bootstrap.php';

$member = \App\User::i();

if(!$member->isLogged() OR $member->role != 'admin' OR !$member->checkCsrf())
    exit();

\App\Uploader::i()->saveFromPOST();
$names = \App\Uploader::i()->getNames();
$name = $names['upload'][0];

if(!$name)
    exit();

echo json_encode([
    'filename' => $_FILES['upload']['name'],
    'uploaded' => 1,
    'url' => $name
]);