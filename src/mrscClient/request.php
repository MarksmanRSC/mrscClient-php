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

class request extends mrscClient {
    public $uri = "/?section=request&action=makeRequest";
    
    /**
     * Unique string to reference your order. Can be used as an RMA number
     * @var string
     */
    public $order_id;
    
    /**
     * Specify what kind of request this is.
     * 
     * 
     * 
     * @var string
     */
    public $requestType;
    
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
    
    public function makeRequest()
    {
        $this->uri = "/?section=request&action=makeRequest";
            
            
        // $this->body["items"] = $this->items;
        
        return $this->send();

    }
    
    /**
     * Adds an item to a request
     * @var returnItem
     */
    public function addItem(returnItem $returnItem) {
        $this->items[] = $returnItem;
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
        
        foreach ($itemList as $itemData) {
            $this->items[] = new returnItem($itemData);
        }
        
    }

}