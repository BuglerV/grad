<?php

$curl = curl_init('http://aska.ru-hoster.com:2199/external/rpc.php?m=streaminfo.get&username=herrpaulchen&rid=herrpaulchen');

curl_exec($curl);