<?php

require_once '../src/autoload.php';

use mrscClient\mrscClient;

$client = new mrscClient();
$client->body = [
    "section" => "item",
    "action" => "getInventory",
    "options" => [
        "search" => array(
            "Condition_Name" => ["LIKE", "Refurb"],
            "Marksman_Category_Name" => ["LIKE", "Laptop"]
        ) 
    ]

];

$inventory = $client->send();

print_r($inventory);
