<?php
/*
 * This file is part of the mrscClient-php software project which is licensed
 * under the BSD 3-Clause License. Please see the LICENSE file located
 * in the root folder of this project.
 * 
 * Copyright 2017 Marksman RSC
 * 
 */
require_once '../src/autoload.php';

use mrscClient\shipment as shipment;

$shipment = new shipment();

// $shipment->order_id = "OUTGOING-333-009-166-495";
$shipment->comment = "Please place fliers with my logo inside all packages. Testing update.";

$shipment->destination_address = array(
    'name' => "John Doe",
    'street1' => "121 E. Paradise St",
    'street2' => "Apt 2",
    'city' => 'Orrville',
    'province' => 'OH',
    'postal_code' => '44667',
    'country' => 'US',
    'phone' => '555-555-5555',
    'email' => 'nobody@marksmanrsc.com'
);



$shipment->addItems(
    array(
        array(
            "sku" => "testsku-125",
            "quantity" => 5
        )
    )
);


$response = $shipment->create();

print_r($response);
