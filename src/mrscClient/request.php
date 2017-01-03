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
    public $order_id;
    public $requestType;
    public $items;
    public $comment;   
    
    public function makeRequest()
    {
        $this->uri = "/?section=request&action=makeRequest&order_id=". $this->order_id. "&requestType={$this->requestType}".
            "&comment=". urlencode($this->comment);
            
        $this->body["items"] = $this->items;
        
        echo $this->send();

    }
    
    public function addItem(returnItem $returnItem) {
        $this->items[] = $returnItem;
    }

}