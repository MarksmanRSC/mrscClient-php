<?php
/*
 * This file is part of the mrscClient-php software project which is licensed
 * under the BSD 3-Clause License. Please see the LICENSE file located
 * in the root folder of this project.
 * 
 * Copyright 2017 Marksman RSC
 * 
 * @author Jason Thistlethwaite
 * @version 0.0.2
 */
namespace mrscClient\Shipping;

class Address {
    /**
     * Name part of address
     * @var string
     */
    public $name;
    
    /**
     * Primary street address (house number)
     * @var string
     */
    public $street_address1;
    
    /**
     * Secondary address (apartment number)
     * @var string
     */
    public $street_address2;
    
    /**
     * Name of city
     * @var string
     */
    public $city;
    
    /**
     * State, province, or region
     * For US please use 2-letter abbreviations
     * @var string
     */
    public $province;
    
    /**
     * Postal code or zip code
     * @var string
     */
    public $postal_code;
    
    /**
     * 2-letter country identifier. Defaults to US
     * @var string
     */
    public $country = 'US';
    
    /**
     * Email address to contact about delivery questions or problems
     * @var string
     */
    public $email;
    
    /**
     * Phone number to call about delivery questions or problems
     * @var string
     */
    public $phone = '513-771-8777';
    
    public function __construct(Array $address = null) {
        if ($address != null) {
            
            foreach ($address as $property => $value) {
                if (property_exists($this, $property)) {
                    $this->$property = $value;
                } else {
                    throw new \Exception("Invalid property '$property'");
                }
            }
            
            // foreach ($address as $property => $value) {
                // if (property_exists( get_class($this), $property) ) {
                    // $this->$property = $value;
                // } else {
                    // throw new \Exception(get_class($this). " : Invalid address property '$property' in address ". print_r($address, true));
                // }
            // }
        }
    }
} 