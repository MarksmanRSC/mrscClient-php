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
class returnItem
{

    const SERVICE_FULL_SERVICE = 2;
    const SERVICE_INSPECTION = 1;
    const SERVICE_RECEIVE_ONLY = 0;

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
     * Amazon FNSKU (FBA Label Barcode)
     *
     * If this is provided along with ASIN we are able to generate FBA labels for your items without you
     * needing to upload an attachment containing them.
     *
     * @var string
     */
    public $fnsku;

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
     * If your item has a unique serial number and you want us to verify
     * the returned item bears thie serial number, you can indicate that
     * by adding a serial_number to the item object
     * @var string
     */
    public $serial_number;

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


    /**
     * @var int Level of service for item; Only matters for returns
     *
     * 2 - Full service
     * 1 - Inspection only
     * 0 - Receive + any specifically requested services
     *
     */
    public $serviceLevel = 2;

    /**
     * Can be passed an array of property => value at initialization
     * @throws Exception on invalid properties
     */
    public function __construct(Array $returnItem = null )
    {
        if ($returnItem != null) {
            foreach ($returnItem as $key => $value) {
                if (property_exists(get_class($this), $key)) {
                    $this->$key = $value;
                } else {
                    throw new \Exception("Unknown property '$key'");
                }
            }
        }

    }    

}
