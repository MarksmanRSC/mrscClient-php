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

/**
 * Item being sent as a return
 * 
 * This is used to add items to a request.
 */
class returnItem {
    
    /**
     * Your unique SKU for this product. Max 40 characters.
     * @var string
     */
    public $sku;
    
    /**
     * UPC for this product
     * @var int
     */
    public $upc;
    
    /**
     * Amazon ASIN for this product.
     * Note: providing this will usually result in faster service
     * @var string
     */
    public $asin;
    
    /**
     * Descriptive name for this product
     * @var string
     */
    public $product_name;
    
    /**
     * Short explanation of reason product was returned
     * Note: This will appear as the intial comment on the item
     *       Please provide this when possible
     * @var string
     */
    public $return_reason;
    
    /**
     * Quantity of product if sending more than 1 of the same item
     * @var int
     */
    public $quantity = 1;
    
    /**
     * Details what should be done with this item
     * The default is to add the item to the request
     * 
     * Valid options are:
     *  add - adds item to the request
     *  cancel - removes the specify item from a request
     * 
     */
    public $action = 'add';
    
    public function __construct()
    {
        
    }
}
