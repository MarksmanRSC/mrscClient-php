<?php
namespace mrscClient;
require_once '../src/autoload.php';


$client = new mrscClient();
$client->body = [ "section" => "item",
     "action" => "getShippingLabelForItemNo", 
     "item_no" => 100097426
     ];
$client->send();
