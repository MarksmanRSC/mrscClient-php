<?php
/*
 * This file is part of the mrscClient-php software project which is licensed
 * under the BSD 3-Clause License. Please see the LICENSE file located
 * in the root folder of this project.
 * 
 * Copyright 2017 Marksman RSC
 * 
 */
require_once '../src/autoload.php';

use mrscClient\request as request;
use mrscClient\returnItem as returnItem;

$request = new request();
$request->order_id = 'EZ-99999-203-242-174-861';
$request->requestType = 'EZ';
$request->comment = "This is my comment about this order";

$request->items = array(
);

$return = new returnItem();
$return->sku='fucking thing';
$return->product_name = "spankbot 9001";

$request->addItem($return);

$request->makeRequest();