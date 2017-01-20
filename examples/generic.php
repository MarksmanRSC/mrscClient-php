<?php
namespace mrscClient;
require_once '../src/autoload.php';


$client = new mrscClient();
$client->body = [ "section" => "request",
     "action" => "getRequest", 
     "order_id" => "my order is nice 25"];
$client->send();
