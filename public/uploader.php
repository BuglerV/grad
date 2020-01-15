<?php

define('BG',true);

require '../bootstrap.php';

if(\App\User::i()->role !== 'admin')
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