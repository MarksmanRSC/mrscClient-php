<?php
namespace mrscClient;
require_once '../src/autoload.php';


$client = new mrscClient();
$client->body = [
    "section" => "request",
    "action" => "getRequest",
    "order_id" => '170115STL' 
];

$client->send();
