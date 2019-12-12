<?php

define('BG',true);

chdir('..');

require '../bootstrap.php';

App\Core::i('admin')->run();