<?php
namespace mrscClient;
require_once '../src/autoload.php';


$client = new mrscClient();
$client->body = [ 
//	"Authentication" => '9082e7881449ddba496c8d9cccdc74957cbb372a075dc932bc4ec0affb85b2a2',
//	"Mimic-Account" => '20025',
	"section" => "shipping",
  "action" => "checkShippingBatch",
	"batch_id" => 15
];
$client->send();
