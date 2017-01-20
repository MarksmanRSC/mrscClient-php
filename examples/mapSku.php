<?php
namespace mrscClient;
require_once '../src/autoload.php';


$client = new mrscClient();
$client->body = [
    "section" => "item",
    "action" => "mapSku",
    "skuMapping" => [
    
        [
            "key" => 5,
            "keyType" => "id",
            "sku" => "sku_sdqwdeff"
        ],
        [
            "sku" => "sku_dqwdrger"
        ]
    
    ]
];

$client->send();
