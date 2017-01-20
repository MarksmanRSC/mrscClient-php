<?php

require_once '../src/autoload.php';

use mrscClient\mrscClient;

$client = new mrscClient();
$client->body = [
    "section" => "item",
    "action" => "getGenericInfo",
    "key" => 'B019II0P77',
    "keyType" => "asin_no"
];

$client->send();
