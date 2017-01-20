<?php

require_once '../src/autoload.php';

use mrscClient\shippingPurchase;
use mrscClient\Shipping\Address;
use mrscClient\Shipping\Package;

$shippingPurchase = new shippingPurchase();

/*
 * Specify destination address
 */
$destination_address = new Address(
    [
        "name" => "Test",
        "street_address1" => "1726 Viking Avenue",
        "street_address2" => " ",
        "city" => "Orrville",
        "province" => "OH",
        "postal_code" => 44667
    ]        
);

/*
 * Specify address you're shipping from
 */
$from_address = new Address(
    [
        "name" => "Marksman 20015",
        "street_address1" => "571 Northland Blvd",
        "street_address2" => " ",
        "city" => "Cincinnati",
        "province" => "OH",
        "postal_code" => 45240
    ]
);

$shippingPurchase->setDestinationAddress($destination_address);
$shippingPurchase->setFromAddress($from_address);

/*
 * Add a package to the shipment
 */
$shippingPurchase->addPackage( new Package(
    [
        "length" => 4,
        "height" => 4.5,
        "width" => 8,
        "weight" => 2
    ]
));


/*
 * 
 */
$response = $shippingPurchase->getRates();

$rates = $response->message;

$purchaseRates = array();
$lowestRates = array();

foreach ($rates as $packageRates) {
    $lowestAmount = false;
    $lowestRate = false;
    
    for ($x = 0; $x < count($packageRates); $x++) {
        if ($lowestAmount == false) {
            $lowestAmount = $packageRates[$x]->amount;
            $lowestRate = $x;
        } else {
            if ($packageRates[$x]->amount < $lowestAmount) {
                $lowestAmount = $packageRates[$x]->amount;
                $lowestRate = $x;
            }
        }        
    }    
    $lowestRates[] = $packageRates[$lowestRate];

}


$purchaseLabel = new shippingPurchase();
$results = $purchaseLabel->purchaseLabel($lowestRates);

print_r($results);
