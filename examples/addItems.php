<?php
namespace mrscClient;
require_once '../src/autoload.php';


$client = new mrscClient();
$client->body = [ "section" => "request",
     "action" => "addItems", 
		 "order_id" => "EZ-20025-221-187-591-495",
     "items" => [
				"100097527"
			]
];
$client->send();
