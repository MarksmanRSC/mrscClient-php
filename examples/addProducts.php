<?php
namespace mrscClient;
require_once '../src/autoload.php';


$client = new mrscClient();
$client->body = [
    "section" => "item",
    "action" => "addProducts",
    "products" => [
    
        [
            "product_name" => "Test product is now different",
            "asin_no" => 'B01D8H09TS',
            "upc" => "124587012457",
            "dimension_height" => 4.5,
            "dimension_length" => 2,
            "dimension_width" => 7,
            "shipping_weight" => 8,
            "product_description" => "This is my product"
        ]    
    
    ]
];

$client->send();
