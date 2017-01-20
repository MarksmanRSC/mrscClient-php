<?php

require_once '../src/autoload.php';

use mrscClient\mrscClient;

$client = new mrscClient();
$client->body = [
    "section" => "item",
    "action" => "getSkuList"

];

$client->send();
