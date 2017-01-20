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

use mrscClient\request as request;
use mrscClient\returnItem as returnItem;

$request = new request();
$request->order_id = "my order is nice 25";
$request->requestType = 'EZ';
$request->comment = "I have changed my comment";
$request->update = false;


// $request->addItem($return);


$request->addItems(
    array(
        0 => array(
            "sku" => "somekindathinga",
            "product_name" => "Not a flashlight",
            "return_reason" => "Wrong item"
        ),        
        1 => array(
            "sku" => "something-else",
            "product_name" => "Blue flashlight",
            "return_reason" => "Defective",
            "quantity" => 2,
        ),
        2 => array(
            "sku" => "something-else",
            "serial_number" => "1337_555"
            )
    )
);


// $response = $request->makeRequest();
$response = $request->send();

print_r($response);
