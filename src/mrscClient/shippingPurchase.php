<?php

namespace mrscClient;

use mrscClient\Shipping\Address;
use mrscClient\Shipping\Package;

class shippingPurchase extends mrscClient
{
    public $destination_address;
    public $from_address;
    
    public $insurance_required = false;
    public $insurance_amount = 0;
    
    public $signature_required = 'no';
    
    public $saturday_delivery = false;
    
    public $packages;
    
    public function addPackage(Package $package)
    {
        $this->packages[] = $package;
    }    
    
    public function setDestinationAddress(Address $destination_address)
    {
        //@todo add validation here
        $this->destination_address = $destination_address;
    }
    
    public function setFromAddress(Address $from_address)
    {
        //@todo add validation here
        $this->from_address = $from_address;
    }
    
    /**
     * Get a list of rates for each package specified in $this->packages
     * Response->message will be a numeric array indexed the same
     * way as the packages you sent
     * 
     * Each key of the array 
     */
    public function getRates()
    {
        $this->uri = "/?section=shipping&action=getRates";
        
        return $this->send();
    }
    

    public function purchaseLabel($rates)
    {
        $this->uri = "/?section=shipping&action=purchase";
        $this->rates = $rates;
        
        return $this->send();
    }
}
