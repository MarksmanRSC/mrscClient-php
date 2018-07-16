<?php
namespace mrscClient;
require_once '../src/autoload.php';


$client = new mrscClient();
$client->body = [ 
//	"Authentication" => '9082e7881449ddba496c8d9cccdc74957cbb372a075dc932bc4ec0affb85b2a2',
//	"Mimic-Account" => '20025',
	"section" => "shipping",
  "action" => "upsMiPurchase",
	"orders" => array(
		[
			"package_id" => "45",
			"to_name" => "Jason",
			"to_addr1" => "1726 Viking Ave",
			"to_addr2" => null,
			"to_city" => "Orrville",
			"to_state" => "OH",
			"to_code" => "44667",
			"weight" => "6",
			"weight_unit" => "OZS",
			"length" => 12,
			"width" => 2,
			"height" => 1,
			"service" => "M4",
			"packing_type" => "Irregulars"
		],
		[
			"package_id" => "",
			"to_name" => "Jason T",
			"to_addr1" => "1726 Viking Ave",
			"to_addr2" => null,
			"to_city" => "Orrville",
			"to_state" => "OH",
			"to_code" => "44667",
			"weight" => "6",
			"weight_unit" => "OZS",
			"length" => 12,
			"width" => 2,
			"height" => 1,
			"service" => "M4",
			"packing_type" => "Irregulars"
		]

	)
];
$client->send();
