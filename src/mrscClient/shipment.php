<?php
/*
 * This file is part of the mrscClient-php software project which is licensed
 * under the BSD 3-Clause License. Please see the LICENSE file located
 * in the root folder of this project.
 * 
 * Copyright 2017 Marksman RSC
 * 
 */
namespace mrscClient;

class shipment extends mrscClient {
    /**
     * Unique string to reference your order. Can be used as an RMA number
     * @var string
     */
    public $order_id;
    
    /**
     * If set true you can modify an existing request by calling this using
     * the order_id
     */
    public $update = false;
    
    /**
     * Specify what kind of request this is.
     * 
     * 
     * 
     * @var string
     */
    public $requestType = 'OUTGOING';
    
    /**
     * Array of items contained in request.
     * @see returnItem
     * @var array
     */
    public $items = array();
    
    /**
     * Comments or extra instructions for your request
     * @var string
     */
    public $comment;   
    
    /**
     * Optional destination address for request
     * @var shippingAddress
     */
    public $destination_address = false;
    
    /**
     * Optional from address for request
     * This defaults to the address for Marksman RSC
     * @var shippingAddress
     */
    public $from_address = false;
    
    public function create()
    {
        $this->uri = "/?section=request&action=createShipment".
            "&comment=". urlencode($this->comment);
            
        $this->body["items"] = $this->items;
        
        return $this->send();        
    }
    
    public function makeRequest()
    {
        return $this->create();
    }
    
    public function setDestinationAddress(Shipping\Address $destination_address)
    {
        $this->destination_address = $destination_address;
    }
    
    public function setFromAddress(Shipping\Address $shippingAddress)
    {
        $this->from_address = $from_address;
    }
    
    /**
     * Adds an item to a request
     * @var array Array containing items to ship
     * 
     * Array format should look like this:
     * 
     * array(
     *   array(
     *      "itemNo" => 10005555
     *   ),
     *   array(
     *      "sku" => "testsku-125",
     *      "quantity" => 4
     *   )
     * )
     * 
     * Each element of the array should be an array with the following
     * keys:
     * 
     *  Either 'itemNo' or 'sku':
     *      itemNo - use this to reference a specific item in your inventory
     * 
     *      sku - use this to add items based on sku
     * 
     *      quantity - required when using sku. specifies how many of that item
     * 
     *      condition - optional; use with sku to limit the item selection
     *                  to products with that sku AND a specific condition
     * 
     * For example:
     *      sku => 'testitem-123',
     *      condition => 'Defective or Damaged'
     *      quantity => 'all'
     * 
     *      Would select all items with sku 'testitem-123' which are in the 
     *      condition 'Defective or Damaged'
     * 
     */
    public function addItem(Array $item) {
        if (!isset($item['itemNo']) && !isset($item['sku'])) {
            throw new Exception("You must specify either itemNo or SKU");
        }
        
        if (isset($item['sku']) && !isset($item['quantity'])) {
            throw new Exception("You must specify a quantity");
        }
        
        if (isset($item['quantity'])) {
            if ( !is_int($item['quantity'] + 0) && $item['quantity'] != 'all') {
                throw new Exception("Quantity must either be an integer or the string 'all'");
            } 
        }
        
        $this->items[] = $item;
    }
    
    /**
     * Add items contained in an array to the request
     * @param Array $itemList
     * 
     * Example:
     * 
     * $itemList = array(
     *  0 => array(
     *      "sku" => 'w2edq3f',
     *      "product_name" => "Some product"
     *      ),
     * 1 => array(
     *      "sku" => "sdqwewefrg",
     *      "product_name" => "Red flashlight",
     *      "return_reason" => "Defective"
     *      )
     * );
     * 
     */
    public function addItems(Array $itemList) {
        foreach ($itemList as $item) {
            $this->addItem($item);
        }
        
    }

}