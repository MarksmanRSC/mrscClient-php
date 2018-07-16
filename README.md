# mrscClient-php

A PHP client for the Marksman RSC API. Marksman RSC is a leading reverse logistics provider for ecommerce sellers. 

[TOC]

## What Can the API Do?

The Marksman RSC API can be used to integrate your existing inventory management, web store, or retail operations with Marksman RSC. Using our API you can:

1. Place return handling requests in our system, and have your returns sent directly to us
   1. Outsource your return handling department directly to us.
2. Monitor the status of products in our facility
3. Generate outbound shipments from our facility
4. Purchase discounted FedEx or USPS labels
   1. Issue discounted shipping labels to your customers when they return products
   2. Receive instant notification when items reach our facility

## Getting Started

The first thing you need to do is create an account at https://app.marksmanrsc.com, and then go under User Account -> Integrations and enable API access to your account. This will grant you an access code and a secret key you will need to authenticate with our system.

You can interact with our API using JSON requests through your own custom client, or use the mrscClient-php library.

## Making API Requests

Each request must be sent to the api endpoint located at either:

https://app.marksmanrsc.com/api.php

or

https://testing.marksmanrsc.com/api.php

The second one is a sandbox version of our site recommended for help learning the API and testing integrations. At this time we don't have a proper "test" mode.

### Anatomy of an API Request

Requests to our API should be HTTP POST requests with the Content-Type set as application/json.

Each request must contain the following, with can appear in either the POST body or the URI:

| Field          | Required     | Description                                                  |
| -------------- | ------------ | ------------------------------------------------------------ |
| mrscAccessCode | Always       | Identifies what account your are accessing. This should be the same as your account number on our website. |
| section        | Always       | The "section" of our API you are accessing. These are outlined later in this document. |
| action         | Always       | The "action" you want to peform.                             |
| timestamp      | SigAuth*     | UNIX timestamp from your computer, adjusted for Eastern Standard Time |
| signature      | SigAuth*     | SHA256 hmac of the request URI and timestamp using your Secret Key as the hmac key. |
| Authentication | SimpleAuth** | Your Secret Key, as provided on the website when you activated API access. |

<u>SigAuth</u>: These fields are required if you're using signature-based authentication

<u>SimpleAuth</u>: This field is required if you're using the "Simple PSK Authentication"



#### body

The "body" of the request, which is where you will pass any additional options or data to the API.

#### A typical example (listing all requests):

URL: /api.php?mrscAccessCode=99999&timestamp=1529591310&signature=4f55235e68a0e944ea2602e42b1aa4d8bb2d1649c2c18a07ba9ed7784896307a

```JSON
{
    "testMode": false,
    "mrscAccessCode": "99999",
    "body": {
        "section": "ajax2",
        "action": "getRequestList",        
        "pageSize": 1
    }
}
```

(The above request is simply asking for the most recent request under the account). Here is an example response:

```JSON
{
    "status": "SUCCESS",
    "apiVersion": "0.2",
    "timestamp": "2018-06-21T10:58:40-04:00",
    "mrscAccessCode": "99999",
    "success": true,
    "message": {
        "total_rows": "15484",
        "columns": [
            "Order Id",
            "Order Placed",
            "Last Updated",
            "Request Type",
            "Request Status",
            "Account No",
            "Items Processing",
            "Total Items",
            "Notes from Marksman",
            "Packages",
            "File Atachments"
        ],
        "rows": [
            {
                "id": "17479",
                "Order Id": "PENDING-983-469-568-049",
                "Order Placed": "2018-06-12 12:33:21",
                "Last Updated": "",
                "Request Type": "UNKNOWN",
                "Request Status": "PENDING",
                "Account No": "99999",
                "Items Processing": "0",
                "Total Items": "0",
                "Notes from Customer": "",
                "Notes from Marksman": "",
                "Packages": "",
                "attachment_ids": "",
                "File Atachments": "",
                "simple_request": ""
            }
        ],
        "query_time": 0.175991,
        "count_time": 0.218109
    },
    "error": null,
    "query": "\/api.php?mrscAccessCode=99999&timestamp=1529593120&signature=f310fa1f1404d40507de149ca5df3fca988889ce06a09e79b2dd9279ee2c0fdd",
    "method": "POST"
}
```



### Authentication

Our API offers two forms of authentication, one more secure than the other.

#### Signature-based Authentication (Higher Security; Less Convenient)

Signature based authorization uses your Secret Key, the current time, and the request URI to generate a signature. This is similar to how the Amazon MWS API performs authentication.

The two primary security features of this method are:

1. Your Secret Key is never transmitted across the network.
2. Because each request's signature is based on the time the request was sent any intercepted requests cannot be "replayed" by a malicious actor.

An example of how to generate this signature:

Example:

```PHP
$currentTime = 1484865559; // UNIX timestamp
$uri = '/api.php?mrscAccessCode=111777&timestamp='. $currentTime;
$signature = hash_hmac('sha256', $uri, $secretKey);
$uri .= '&signature='. $signature;
```

#### Simple PSK Authentication

A simple, but less secure form of authentication is to simply include your Secret Key inside the request body as "Authentication".

Here is an example:

```JSON
{	
	"body": {
        "Authentication": "...secret key here...",
    	"section": "user",
    	"action": "ping"
     }
}
```

### Testing API Connection

You can test API access by sending the following request:

```json
{	
	"body": {
    	"section": "user",
    	"action": "ping"
     }
}
```
You should receive a response like this:

```json
{
  "status": "SUCCESS",
  "apiVersion": "0.2",
  "timestamp": "2017-01-19T17:36:46-05:00",
  "mrscAccessCode": "99999",
  "success": true,
  "message": null,
  "error": null,
  "query": "\/api.php?mrscAccessCode=99999&timestamp=1484865406&signature=c50ea81642dff85ef3e28639d085389386f63c6e535852764ee9528aa4d85de2",
  "method": "POST"
}
```
### Common Response Fields & Meanings:

| Field          | Meaning                                                      |
| -------------- | ------------------------------------------------------------ |
| status         | textual indication of whether your request succeeded or failed. Typically "**SUCCESS**" or "**FAILURE**" |
| success        | Boolean **true** or **false**, representing success or failure. |
| error          | On failure, this will include a string explaining what the problem is. |
| timestamp      | The date and time according to our server, expressed in a subset of the ISO 8601 standard. Example: 2018-06-21T11:02:41-04:00 |
| apiVersion     | The version of the API you are communication with. At the time of writing there is only one version. This will be used in the future to mitigate any compatibility issues between updates. |
| mrscAccessCode | The account number under which your request was processed. This should always be your own account number unless you have used the Mimic-Account functionality to access a sub-account (this feature is not currently accessible to most customers). |
| message        | A JSON object containing the results of your request. The exact format of the data within will depend on the API *section* and *action* you have called. |



# API Sections Overview

Our API is divided into several sections as follows:

| Section  | General Usage                                                |
| -------- | ------------------------------------------------------------ |
| item     | Check status of inventory, pull detailed information about specific products, register and update product template/SKU information. |
| request  | Place incoming or outgoing requests, list requests and statuses, check for new requests/shipments |
| shipping | Purchase shipping labels                                     |
| user     | Various account-related information and functions            |
| ajax2    | This API section is used for many of the pages on our website and can be used to access some detailed reports or information in more flexible ways than other API sections. This section is still a work in progress. |









## Item

Allows checking status on individual items, stock levels, managing skus, and several other functions related to products.

### getItemInfo

Parameters:

**item_no**	This is the unique SKU or Item Number Marksman assigns to items at checkin.

This request will return a dump of information about the specified item, including condition, resale value, and comments about the condition of the item.

```json
           "iproduct_id": "4169",
            "ASIN": "B015PYZ0J6",
            "FNSKU": "X000XP50XZ",
            "iproduct_name": "Dell Inspiron i7559-2512BLK 15.6 Inch FHD Laptop (6th Generation Intel Core i7, 8 GB RAM, 1 TB HDD + 8 GB SSD) NVIDIA GeForce GTX 960M",
            "request_id": "638",
            "outgoing_request_id": null,
            "itemNo": "100092431",
            "company_id": null,
            "user_id": "62",
            "checkin_date": "2016-06-06 16:06:51",
            "box": "930",
            "outgoing_box_id": null,
            "Item_Condition": "2",
            "Serial Number": null,
            "itemComment": "Depot service failed to correct: Can\\'t boot",
            "consignment": "0",
            "location": "AX2C3",
            "merchant_sku": null,
            "serviceLevel": "2",
            "product_id": "1354",
            "gproduct_name": "Dell Inspiron i7559-2512BLK 15.6 Inch FHD Laptop (6th Generation Intel Core i7, 8 GB RAM, 1 TB HDD + 8 GB SSD) NVIDIA GeForce GTX 960M",
            "product_detail_link": "http:\/\/www.amazon.com\/Dell-Inspiron-i7559-2512BLK-Generation-GeForce\/dp\/B015PYZ0J6%3Fpsc%3D1%26SubscriptionId%3DAKIAIKRYDR3R75D2V3DQ%26tag%3Dcropcroprole%26linkCode%3Dxm2%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB015PYZ0J6",
            "parentASIN": "B016VLEM5K",
            "dimension_height": "3.9",
            "dimension_length": "20.5",
            "dimension_width": "13.1",
            "dimension_unit": "INCHES",
            "weight": null,
            "shipping_weight": "8.85",
            "weight_unit": "POUNDS",
            "product_picture": "http:\/\/ecx.images-amazon.com\/images\/I\/419DWJfUXuL._SL75_.jpg",
            "upc": "884116204107",
            "ean": "0715407512802",
            "product_category": "609",
            "marksman_category": "8",
            "List Price": "79999",
            "New Price": "87800",
            "Used Price": "73999",
            "Brand": "Dell",
            "Model": "i7559-2512BLK",
            "Marksman_Category_Name": "Laptop",
            "Marksman_Category_Description": null,
            "Marksman_Category_Dollars": "20",
            "Marksman_Category_Cents": "0",
            "inspection": "1",
            "fullService": "1",
            "Condition_Id": "2",
            "Condition_Name": "Refurb Approved",
            "Condition_Description": "Customer has approved this product for refurb processing",
            "Condition_Final": "0",
            "Approval_Required": "0",
            "consignment_modifier": "0.00",
            "discount": "0.00",
            "Sale Price": "79999.00",
            "SKU": null
```
#### Item Info Fields and Meanings

##### iproduct_id

Our internal id for the specific item

##### ASIN

The Amazon ASIN for this item.

##### FNSKU

The FNSKU of your item. This is set at the time a return handling request is made if the item was returned from Amazon FBA.

The FBA (Fulfillment by Amazon) SKU (**FNSKU**) is an Amazon product identifier for products that are fulfilled by Amazon. The **FNSKU** identifies the product as yours. You need an **FNSKU** in order to create FBA Inbound Shipments. To get the **FNSKU**, set the product as Fulfilled by Amazon, and then launch it to Amazon.

##### iproduct_name

This is the title, or name, of the individual product. This is not to be confused with **gproduct_name**, as they are often the same. This field exists in case, for example, a returned product is supposed to be one product but is actually something else

##### request_id

This is the internal id of the request in which we received this item.

##### outgoing_request_id

If set, this indicates the internal id of an outbound shipment the item belongs to.

##### itemNo

When items are received in our facility we assign each one a unique barcode with an item number on it. This field indicates the item number assigned to your product.

If this field is blank or null it indicates your product was not assigned an item number.

##### company_id

Unused at this time.

##### user_id

This indicates who the product belongs to. This is the internal id of the user account, not the user account number displayed on our website.

##### checkin_date

This is the date and time the product was checked in at our facility. "Checked in" is not necessarily the same thing as "received". Checked in means the package the item arrived in was photographed and the item was routed for processing (either for refurbishment, forwarding, fba preparation, etc).

Items without a checkin date have not yet begun processing in our system.

##### box

This is the internal id of the package in which we received your item.

##### outgoing_box_id

If your item has been sent out from our facility this is the internal id of the package your item was shipped within

##### Item_Condition [DEPRECATED]

Please see **Condition_Id**

##### Serial_Number

This is the serial number on your item. This field will be available if you have asked us to record the serial numbers of items in a request.

##### consignment

This field is either "0" or "1". It indicates whether or not this item is marked for consignment sale by Marksman RSC.

##### location

This is the location of the item in our facility.

##### merchant_sku

This is a string indicating your SKU for this item. This should match against the SKU records you have with us.

##### service_level

This indicates the level of service you have indicated for this item.

###### 0: Receiving and prep only

###### 1: Inspection

###### 2: Full service

##### product_id

This is the generic product, or product template, this item is an instance of. Please see **getGenericInfo**

##### gproduct_name

This is the name of the product template this product is an instance of.

##### product_detail_link

This is a URL to where details about the product's template can be viewed. This is typically a link to Amazon.com or another market place which has details about the item.

##### parentASIN

This field is only populated for products sold through Amazon.com. This field represents the parent ASIN, or product, of which this product is a variant.

##### dimension_height

The height of this product, taken from the product template.

##### dimension_length

The length of the product, taken from the product template.

##### dimension_width

The width of the product, taken from the product template.

##### dimension_unit

This is the unit in which the length, height, and width are measured.

##### shipping_weight

This is the average estimated weight of this item for shipping purposes. It is generally safe to use this to estimate shipping costs for a product individually. However, it should not be used (unless rounded up) for estimating shipping for multiple items in the same shipment.

##### weight_unit

The unit shipping_weight is measured in

##### product_picture

A URL where a picture of this product can be found. This is taken from the product template.

##### upc

Universal Product Code for the item's product template

##### ean

##### product_category

Unused at this time.

##### marksman_category

Numeric representation of the service category used for this item by Marksman. The category is how we determine service options and pricing for refurbishing and inspecting returned merchandise.

##### List Price

This is generally the **buy box price** as seen on Amazon.com, expressed as USD in integer format. Example: 1195 = $11.95 USD.

##### New Price

This is the lowest new price for this product on Amazon.com

##### Used Price

This is the lowest used price for this product on Amazon.com

##### Brand

This is the brand or manufacturer of the product. This is here simply to make it easier to query products.

##### Model

This is the model number of the product.

##### Marksman_Category_Name

This is the textual name of the service category used by Maksman RSC for this item.

##### Marksman_Category_Description

This is a text description of what kind of items belong in this category.

##### Marksman_Category_Dollars

##### Marksman_Category_Cents

These two fields indicate the price Marksman RSC charges for full-service for this item.

##### inspection

Either "0" or "1". This indicates if inspection services are available for this item based on the category it's in.

##### fullService

Either "0" or "1". This indicates if Full Service refurbishment is available for this item based on it's category.

##### Condition_Id

A numeric indicator of the condition an item is in. Please see the documentation section about **Item Conditions**. 

##### Condition_Name

The text name of the condition an item is in.

##### Condition_Description

Text description of what the Condition means.

##### Condition_Final

Either "1" or "0". A value of "1" indicates the item is in a "final condition", which means no further service for the item is scheduled or anticipated.

##### Approval_Required

Approval Required indicates your approval is required before the next step in service can be completed. This is either "0" or "1".

##### consignment_modifier

This is a floating point value representing how much commission Marksman will take from this item if sold through consignment. This only applies to items marked for consignment.

##### discount

Unused

##### Sale Price

This indicates the price Marksman's algorithms recommend selling this item for based on it's condition.

### getGenericInfo

Parameters:

**key	** - Key to look up generic product template

**keyType	** - Specifies the type of key used to look up the item template

Valid keyType's:

- upc
- ean
- asin_no - ASIN number for the product on amazon.com
- id - our internal ID for the product template (as returned in getItemInfo and some other areas of the API)
- sku - Your own sku for the product template

**useAmazon** - Attempt to import the product template from Amazon. true or false

This request returns information like this:



```json
"status": "SUCCESS",
"apiVersion": "0.2",
"timestamp": "2017-01-20T11:04:21-05:00",
"mrscAccessCode": "99999",
"success": true,
"message": {
    "id": "254333",
    "date_created": "2016-10-23 15:30:30",
    "last_modified": "2017-01-16 07:32:22",
    "product_name": "THINKPAD ONELINK+ DOCK",
    "product_detail_link": "http:\/\/www.amazon.com\/Lenovo-40A40090US-THINKPAD-ONELINK-DOCK\/dp\/B019II0PHW%3FSubscriptionId%3DAKIAIKRYDR3R75D2V3DQ%26tag%3Dcropcroprole%26linkCode%3Dxm2%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB019II0PHW",
    "product_description": null,
    "asin_no": "B019II0PHW",
    "parent_asin": null,
    "dimension_height": "2.5",
    "dimension_length": "9.8",
    "dimension_width": "7.9",
    "dimension_unit": "INCHES",
    "weight": null,
    "weight_unit": "POUNDS",
    "shipping_weight": "2.05",
    "product_picture": "http:\/\/ecx.images-amazon.com\/images\/I\/41FHFiL53OL._SL75_.jpg",
    "upc": "889800394355",
    "ean": "0889800394355",
    "full_info": "1",
    "product_procedure": null,
    "product_category": "48",
    "marksman_category": "499",
    "needs_update": "0",
    "new_price": "13755",
    "list_price": "14494",
    "used_price": "12319",
    "manufacturer_id": "86",
    "brand": "Lenovo",
    "model": "40A40090US",
    "warranty": null,
    "release_date": null,
    "creator": null
},
"error": null,
"query": "\/api.php?mrscAccessCode=99999&timestamp=1484928261&signature=ed4a988e74bd6c7dd1f2523524a5207e1c69891f941fa511f7d0aa5e914359bf",
"method": "POST"
```
### getSkuList

Parameters:

This action does not take any parameters.

#### Response:

This call lists all of your SKU's our system knows about. Please note that in our system a SKU is basically just your own unique ID for a generic product. The product_id field in the response body is our internal ID for the product which can be passed as a key to *getGenericInfo*

```json
"status": "SUCCESS",
"apiVersion": "0.2",
"timestamp": "2017-01-20T11:07:02-05:00",
"mrscAccessCode": "99999",
"success": true,
"message": [
    {
        "id": "162867",
        "sku": "somekindathinga",
        "product_id": "303335",
        "product_name": "Not a flashlight",
        "upc": null,
        "ean": null,
        "asin_no": null,
        "product_picture": null,
        "new_price": "0",
        "list_price": "0",
        "used_price": "0"
    },
    {
        "id": "162868",
        "sku": "something-else",
        "product_id": "303336",
        "product_name": "Blue flashlight",
        "upc": null,
        "ean": null,
        "asin_no": null,
        "product_picture": null,
        "new_price": "0",
        "list_price": "0",
        "used_price": "0"
    },
    {
        "id": "162866",
        "sku": "testsku-125",
        "product_id": "303334",
        "product_name": "testsku-125",
        "upc": null,
        "ean": null,
        "asin_no": null,
        "product_picture": null,
        "new_price": "0",
        "list_price": "0",
        "used_price": "0"
    }
],
"error": null,
"query": "\/api.php?mrscAccessCode=99999&timestamp=1484928422&signature=e9ba4aed9c3ca2bcddef24de3ccc4465761c3c74074271aa8ec6e97d016b63b6",
"method": "POST"
```


### getInventory

This call returns a list of items currently located at the Marksman RSC facility.

A response will look like this:

```JSON
"status": "SUCCESS","apiVersion": "0.2",
"timestamp": "2017-01-20T11:58:11-05:00",
"mrscAccessCode": "20025",
"success": true,
"message": {
    "total_records": 12,
    "records_returned": 12,
    "options": {
        "search": {
            "Condition_Name": [
                "LIKE",
                "Refurb"
            ],
            "Marksman_Category_Name": [
                "LIKE",
                "Laptop"
            ]
        }
    },
    "inventory": [
      { item },
      { item },
      { item }....
    ],
"error": null,
    "query": "\/api.php?mrscAccessCode=20025&timestamp=1484931491&signature=c07e54d60a77b5a6678817a95bede4809ed792338a30c5818b4eef603f78e4f4",
    "method": "POST"
```
##### total_records

The total number of records matching your query

##### records_returned

The number of item records returned.

##### options

Shows how our system understood any options you passed.

##### inventory

This array contains records of each matching product in inventory. The objects contained are of the same format returned by **getItemInfo**



Parameters

No parameters are required. However, several optional parameters are allowed, and should be passed as an associate array or object **options**

#### Valid Options:

##### search

Search is an associate array of columns to check against values. It is a limited form of a SQL WHERE clause.

Example:

```json
"mrscAccessCode": "20025",
"body": {
    "section": "item",
    "action": "getInventory",
    "options": {
        "search": {
            "Condition_Name": [
                "LIKE",
                "Refurb"
            ]
        }
    }
}
```
Each search term column name, followed by an operator, and a value. The contents of the returned columns will be compared against the value using the specified operator.

Allowed operators include:

* =
* !=
* IS
* IS NOT
* LIKE

Multiple search criteria are ANDed together.

```json
"mrscAccessCode": "20025",
"body": {
    "section": "item",
    "action": "getInventory",
    "options": {
        "search": {
            "Condition_Name": [
                "LIKE",
                "Refurb"
            ],
            "Marksman_Category_Name": [
                "LIKE",
                "Laptop"
            ]
        }
    }
}
```
The above results in a WHERE clause like this:

```mysql
AND (
	Condition_Name LIKE '%Refurb%' 
	AND Marksman_Category_Name LIKE '%Laptop%'
)
```
### addProducts

This action allows you to create or update product templates, or "generic" products in our system. You can only update your own templates.

Templates which contain a valid UPC, EAN, or ASIN are considered global templates and can only be edited by the person who created them. In some cases a global template becomes "locked", meaning our system pulls information from a trusted source to update the template and the user who created it can no longer change these details.

```json
"body": {
    "section": "item",
    "action": "addProducts",
    "products": [
        {
            "product_name": "Test product",
            "asin_no": "B01D8H09TS",
            "dimension_height": 4.5,
            "dimension_length": 2,
            "dimension_width": 7,
            "shipping_weight": 8,
            "product_description": "This is my product"
        }
    ]
}
```
##### Allowed Parameters

* product_name
  * Name of the product. Max 255 characters
* product_detail_link
  * Link to information about product. Max 255 characters
* product_description
  * Text description of product. Max 255 characters
* asin_no
  * ASIN for product on Amazon.com
* parent_asin
  * Parent ASIN for product on Amazon.com
  * **You can use this field instead of ASIN if you are trying to create a variant template of a global template**
* dimension_height
  * Float; height of product
* dimension_length
  * Float; length of product
* dimension_width
  * Float; width of product
* shipping_weight
  * Float; average estimated weight of product when packaged for shipping
* dimension_unit
  * Unit of measurement used for height, length, width. Defaults to INCHES
* weight
  * Weight of unit
* weight_unit
  * Measurement unit for product weights. Defaults to POUNDS
* product_picture
  * URL to picture of the product. For integration purposes this image should be no larger than 75x75 pixels and must not require authentication to access
* upc
* ean
* brand
  * Brand or manufacturer of the product. Max 255 characters
* model
  * Model number of product. Max 255 characters
* warranty
  * Description of warranty coverage for the product. Max 255 characters

##### Special Parameters

Specify any of the following parameters to update a matching, existing template:

###### id - our internal product_id

###### asin_no

###### upc

###### ean

*Please note: you cannot update a global template (has valid upc, ean, or asin_no) unless you are the one who created it.*



## Request

Request allows you to place requests for service, generate outbound shipments, and check the status of requests placed with us.

### addItems [internal]

This function is currently only accessible internally.

#### Parameters:

##### order_id - The order_id of the request

**request_id** - Internal id of the request

##### force - boolean

**items** array of item numbers

This action will take the given item numbers and add them to the specified request. This will fail if their is an item or request ownership issue or the items are in invalid conditions for the request.

Passing **force=true** ignores validation.

If adding items to an incoming request the incoming box of the item will be set to null. If adding items to an outgoing request the outgoing_box_id of the items will be set to null.



### getRequest

Returns information about a request in our system, including all products in the request.

##### Parameters:

Must specify one of the other:

**order_id** - The order_id or reference number of the request

**request_id** - The internal id of the request object

##### Example Request:

    "body": {
        "section": "request",
        "action": "getRequest",
        "order_id": "170115STL"
    }
##### Example Response:

```json
{
    "status": "SUCCESS",
    "apiVersion": "0.2",
    "timestamp": "2018-02-15T20:17:49-05:00",
    "mrscAccessCode": "99999",
    "success": true,
    "message": {
        "id": "10619",
        "company_id": "0",
        "user_id": "1",
        "request_date": "2018-02-15 20:17:49",
        "modify_date": null,
        "order_id": "EZ538-090-981-829",
        "request_status": "PENDING",
        "return_reason": null,
        "request_type": "EZ",
        "items_received": "0",
        "items_total": "7",
        "fee_dollars": "0",
        "fee_cents": "0",
        "comment": null,
        "customer_notes": "Bullshit\\\\n\\\\n",
        "marksman_ships": false,
        "pending": false,
        "expected_packages": null,
        "redmineTicket": null,
        "items": [ .... ], // array of items as with getItems
        "attachments": [
            {
                "id": "10081",
                "user_id": "1",
                "file_name": "yes.pdf",
                "file_type": "application\/pdf",
                "file_size": "489991",
                "upload_date": "2018-02-15 20:17:49",
                "is_shipping_label": "0",
                "file_local_url": "\/home\/jason\/projects\/inventory_us\/src\/uploads\/\/a20$
            }
        ],
        "packages": [
            {
                "id": "13025",
                "is_outgoing": null,
                "tracking_number": "SD3FERFEREGEGETG",
                "company_id": "0",
                "user_id": "1",
                "location": null,
                "request_id": "10619",
                "order_id": null,
                "received_date": "2018-02-15 20:17:49",
                "receiver_id": null,
                "picture_box": null,
                "picture_shipping_label": null,
                "picture_packing_slip": null,
                "picture_contents": null,
                "item_quantity": "0",
                "estimated_weight": null,
                "weight_pending": null,
                "actual_weight": "5.2",
                "checkin_user": null,
                "checkin_date": null,
                "length": "12",
                "height": "8",
                "width": "24",
                "comment": null,
                "alternate_lookup": null,
                "updated_tracking": "0",
                "disposed": "0",
                "disposal_date": null,
                "disposal_user": null
            }
        ],
        "optional_services": [
            {
                "name": "REPACKAGE",
                "extra_instructions": "Place all items in steel containers"
            },
            {
                "name": "RELABEL",
                "extra_instructions": "Do the stickers"
            },
            {
                "name": "PALLETIZE",
                "extra_instructions": "Add some things to the pallet"
            },
            {
                "name": "TAKE PICTURE",
                "extra_instructions": "If it is on fire"
            },
            {
                "name": "RECORD SERIAL NUMBER",
                "extra_instructions": "For all damaged items"
            },
            {
                "name": "INSERTS",
                "extra_instructions": "Put a new manual in each unit"
            },
            {
                "name": "REPAIR AUTHORIZED",
                "extra_instructions": "35"
            }
        ],
                "skuInformationNeeded": []
    },
    "error": null,
    "query": "\/api.php\/?section=request&action=getRequest&mrscAccessCode=99999&timestamp=1$
    "method": "POST"
}
```
| Field               | Meaning                                                      |
| ------------------- | ------------------------------------------------------------ |
| id                  | This is the internal database id of the newly created request. You can use this in other parts of the API to refer to the request. |
| company_id          | This is the company which owns the request. [Not yet implemented] |
| user_id             | Database id of the account that owns the request             |
| request_date        | Date and time request was created                            |
| modify_date         | The last time the request was changed. This is triggered by status updates, changes to the comment, etc. |
| order_id            | The order id assigned to the request. **This should be recorded. It may be different than what you asked for.** This will be automatically generated if you left order_id blank when creating the request. |
| request_status      | The status of your request. This is explained elsewhere.     |
| return_reason       | DEPRECATED; This is a note about why the order is being sent. |
| request_type        | The type of request it is.                                   |
| items_received      | Integer of how many items Marksman has received already.     |
| items_total         | Total count of items in the request.                         |
| fee_dollars         | DEPRECATED                                                   |
| fee_cents           | DEPRECATED                                                   |
| comment             | Marksman's internal notes about the request.                 |
| customer_note       | Customer-provided extra instructions                         |
| marksman_ships      | Boolean. Indicates if Marksman is supposed to generate shipping labels for the request. |
| pending             | Boolean. True indicates the order is a draft.                |
| expected_packages   | How many packages expected to be received.                   |
| redmineTicket       | If the request has a Redmine ticket to track status the ticket number will be here. |
| skuInfomationNeeded | List of SKUs in the request which were newly created.        |
| packages            | Array of packages in the request                             |
| items               | Array of items in the request                                |
| attachments         | Array of attached files                                      |
| optional_services   | Array of optional_services for the request.                  |

### getRequestByTrackingNumber

Locate and return a request based on the tracking number of an associated package.

This action takes only one parameter: **tracking_number**

The response is the same as getRequest.

### makeRequest

This action allows you to place an inbound request in our system, letting us know you are sending items for us to service.

| Field       | Type                | Required | Usage                                                        |
| ----------- | ------------------- | -------- | ------------------------------------------------------------ |
| order_id    | String; max 255     | N        | A code used to identify the request. If one is not provided our system will generate a random one for you. |
| requestType | String              | Y        | The type of order you are sending.                           |
| items       | Array               | N        | An array of the products you are sending.                    |
| packages    | Array               | N        | An array of packages being sent. Includes tracking numbers and dimensions |
| attachments | Array               | N        | Array of base64 encoded files to attach to the request       |
| comment     | String              | N        | A comment or extra instructions for the request. Will be seen by our receiving staff when the order arrives. |
| update      | Bool; default false | Y        | If set to true and the order_id you pass matches an existing request your API call will modify the existing request. You can use this to add items, packages, or attachments as well as update the comment. |

#### Parameters:

##### order_id

This is a unique identifier for your request. If left blank we will randomly generate one for you.

##### requestType

The request type indicates the type of work you want performed on the items you are shipping, and indicates to us the origin of the items so we can process them more efficiently.

Valid values:

- **EZ** - indicates the items are being sent for returns processing or some kind of inspection/testing.
- **PREP** - Indicates the items are new merchandise that do not require any testing, repairs, or similar services. Use this for FBA prep, sending items for fulfillment, or sending us any kind of spare parts or materials.
- **FORWARDING** - Indicates the order is packages you want us to receive without opening and later ship out.


- **FBA** - indicates the items are returned merchandise sold through Amazon fulfillment and being shipped to us from Amazon.
- **FBM** - indicates the items are returned merchandise shipping to us from your buyer.

It is important you select the correct type of request for best service. Selecting the wrong type of request for your items can result in significant delays in service.

**Note about FBA and FBM**: These are both hold-overs from a time Marksman only served Amazon sellers. They have the same effect as EZ and you are not required to use them. However, we ask that you do if you can because it helps us keep better metrics on inbound orders.

##### comment

Optional field; allows you to specify extra instructions or comments about your request. These comments will be visible to our receiving and refurbishment teams.

*Please try to be clear and succinct about any special requirement you have.*

##### items

Items is an array of objects representing the items you are sending in the request. Each item has the following fields:

| Field | Required | Usage                  |
| ----- | -------- | ---------------------- |
| sku   | No*      | Your SKU for the item. |
| upc | No* | UPC for the item |
| asin | No* | Amazon ASIN for the item |
| product_name | No | The title of the product as it would appear for sale. This is only needed if you only provided SKU and you have never sent the item to us before. |
| return_reason | No | A text note about why you are sending the item to us. Our refurb staff will see this note when servicing the item. **Please note**: This field should only be used for items being sent for return handling. It does nothing for FORWARDING or PREP orders. |
| quantity | Y | How many of this item you are sending |
| serial_number | N | The serial number of the item. Provide this if you want us to double check the serial number of the item received matches. |  |
| serviceLevel | N | Only for returns. This selects which level of service you want. 2 = full service, 1 = visual inspection only, 0 = receive only. The default is 2. |


*: You have to provide one of these fields but not all of them.

Here is a valid example:


```json
	[
        "sku": "US_7867676",
        "quantity": 5,
        "return_reason": "Please make sure batteries are included"
	],
	[
        "sku": "US_64435454",
        "quantity": 1,
        "serial_number": "RD767678676",
        "serviceLevel": 0,
        "return_reason": "Please just check the serial number matches"
	],
	[
        "upc": 07084700329,
        "quantity": 24
	]
```

##### packages

An array telling us about what packages you are sending. This is completely optional, but has the following uses:

1. When you provide tracking numbers this way you do not need to place the order_id on the outside of the package.
2. This is useful for making FORWARDING orders.
3. This may help us identify your delivery and speed up intake of your items.

| Field           | Type            | Required | Usage                                                        |
| --------------- | --------------- | -------- | ------------------------------------------------------------ |
| tracking_number | String          | N        | The tracking number of the package.                          |
| comment         | String; max 255 | N        | A note about your package. Will be stored for later and visible in your account. |
| length          | Float           | N        | Length in inches                                             |
| width           | Float           | N        | Width in inches                                              |
| height          | Float           | N        | Height in inches                                             |
| actual_weight   | Float           | N        | Weight in pounds. Do not confuse this with estimated_weight in our API. |

##### attachments

An array containing files you want to attach to your request.

| Field     | Type            | Required | Usage                                |
| --------- | --------------- | -------- | ------------------------------------ |
| file_name | String          | Y        | Name of the file                     |
| data      | String (base64) | Y        | The file encoded as a base64 string. |

##### optional_services

This is an array of "optional services" requested for the request. These represent value-added services you may want performed.

| Field   | Type            | Required | Usage                                                        |
| ------- | --------------- | -------- | ------------------------------------------------------------ |
| service | String          | Y        | Specify which optional service is required                   |
| notes   | String; max 255 | N        | Add additional notes or requirements about how the service should be performed. |

###### List of Optional Services

| Service              | What it means                                                |
| -------------------- | ------------------------------------------------------------ |
| REPACKAGE            | Some kind repackaging is requested. This can be:             |
| RELABEL              | Items need labels replaced or stickers removed. Please specify what labels need changed. |
| PALLETIZE            | Used for OUTGOING requests. This instructs the shipping team to palletize the shipment. |
| TAKE PICTURE         | Take pictures of the items if any defect or damage is found. The note should list any special instructions about when or how to take pictures. |
| RECORD SERIAL NUMBER | Record serial numbers of all items in the request. Extra notes may give more detailed instructions, such as "Record serial number only for damaged units." |
| INSERTS              | Specifies that some items in the request need to be combined/bundled or have other items included in the packaging. The note should either provide specific instructions or reference a file attachment which includes instructions. |
| REPAIR AUTHORIZED    | Pre-authorize additional repairs or services that will improve the resale value of items. The note can include instructions or a dollar amount. See Appendix: Pre-Authorized Repairs for a better understanding. |



#### Examples:

##### Return handling order

This example places a return handling order containing several different items with different levels of service selected for each.

```json
"action": "makeRequest"
"order_id": "my order is nice 25",
"requestType": "EZ",
"optional_services": [
    {
        "service": "TAKE PICTURE",
        "notes": "Any items with physical damage"
    },
    {
        "service": "RECORD SERIAL NUMBER"
    }
],
"items": [
    {
        "sku": "somekindathinga",
        "return_reason": "Wrong item; just check if it is the correct item",
        "quantity": 1,
        "serviceLevel": 1
    },
    {
        "sku": "something-else",
        "return_reason": "Defective",
        "quantity": 2,       
        "serviceLevel": 2
    },
    {
        "sku": "something-else",
        "quantity": 1,
        "serial_number": "1337_555",
        "serviceLevel": 1
    }
],
"comment": "Here are some extra instructions",
"packages": [
    [
        "tracking_number": "....",
        "comment": "My notes about this package",
        "length": 18,
        "width": 12.5,
        "height": 4,
        "actual_weight": 5.2
    ]
],
"attachments": [
    [
        "file_name": "packing_slip.pdf",
        "data": ...base64 encoded file...
    ]
]
"update": false
```

**Example Response:**

```json
"status": "SUCCESS",
"apiVersion": "0.2",
"timestamp": "2017-01-23T11:59:03-05:00",
"mrscAccessCode": "20025",
"success": true,
"message": {
    "id": 3126,
    "company_id": 0,
    "user_id": "62",
    "request_date": "2017-01-23 11:59:03",
    "modify_date": null,
    "order_id": "my order is nice 25-0",
    "request_status": "PENDING",
    "return_reason": null,
    "request_type": "EZ",
    "items_received": 0,
    "items_total": 4,
    "fee_dollars": 0,
    "fee_cents": 0,
    "comment": null,
    "customer_notes": "I have changed my comment",
    "marksman_ships": false,
    "items": [
      // see getRequest
    ],
    "packages": [
      // see getRequest 
    ],
    "optional_services": [
      // see getRequest  
    ],
    "skuInformationNeeded": [
        "somekindathinga",
        "something-else"
        /**
        * These two SKUs show here because this request is the first time the Marksman
        * system has seen them. It indicates you may need to provide more information
        * about them, such as product name, dimensions, etc.
        */
    ]
},
"error": null,
"query": "\/api.php\/?section=request&action=makeRequest&mrscAccessCode=20025&timestamp=1485190743&signature=9361a5cc9545b49d494f9674b066ff76b0ecb4f083aeb04438c009383433fae4",
"method": "POST"
```
##### Forwarding Order

```json
"action": "makeRequest"
"order_id": "6516815615641511",
"requestType": "FORWARDING",
"items": null,
"comment": "When these packages arrive please place new shipping label and mail out.",
"packages": [
    [
        "tracking_number": "....",
        "comment": "My notes about this package",
        "length": 18,
        "width": 12.5,
        "height": 4,
        "actual_weight": 5.2
    ],
    [
        "tracking_number": "51515156156165156455315"
    ],
    [
        "tracking_number": "51515615641515151561561561"
    ],
    [
        "tracking_number": "84848178484181515151511515"
    ]
],
"attachments": [
    [
        "file_name": "new_shipping_labels.pdf",
        "data": ...base64 encoded file...
    ]
]
"update": false
```

#### Response

The response is the same as with get getRequest.



### createShipment

This is used to create an outbound shipment from our facility. This is roughly the same as going to Inventory -> Ship Refurbished Items on our website.

Each outbound shipment consists of:

#### order_id

An unique identifier for your order, such as the Amazon or eBay order id. If this isn't specified our system will generate one for you.

#### requestType

This should always be "OUTGOING" at this point in time, but in the future this will be used to indicate different types of outgoing requests needing different kinds of services.

#### comment

A text comment including any special shipping or packing instructions.

#### items

An array of items you want to have shipped out. Each element of the array must include either a **sku** and **quantity** or an item number for the specific item you want to ship.

#### Example Request:

```json
"order_id": "102-1334441-555",
"update": false,
"requestType": "OUTGOING",
"comment": "Please pack this shipment on a pallet.",
"items": [
  {
    "sku": "testsku-125",
    "quantity": 5
  },
  {
    "item_no": '10009875'
  }
]

```
The above would create an outbound request with the order id 102-1334441-555 containing the specific item *10009875* and a total of 5 items with the sku *testsku-125*

The response to a successfully created request looks like this:

```json
"status": "SUCCESS",
"apiVersion": "0.2",
"timestamp": "2017-01-25T14:34:51-05:00",
"mrscAccessCode": "20025",
"success": true,
"message": {
    "id": 3131,
    "company_id": 0,
    "user_id": "62",
    "request_date": "2017-01-25 14:34:51",
    "modify_date": null,
    "order_id": "OUTGOING-719-995-354-587",
    "request_status": "PENDING",
    "return_reason": null,
    "request_type": "OUTGOING",
    "items_received": 0,
    "items_total": 5,
    "fee_dollars": 0,
    "fee_cents": 0,
    "comment": null,
    "customer_notes": "Please place fliers with my logo inside all packages. Testing update.",
    "marksman_ships": false,
    "items": [
    	{
          "iproduct_id": "11645",
          "ASIN": null,
          "FNSKU": null,
          "iproduct_name": "testsku-125",
          "request_id": "3128",
          "outgoing_request_id": "3131",
          "itemNo": null,
          "company_id": null,
          "user_id": "62",
          "checkin_date": "2017-01-25 14:33:19",
          "box": null,
          "outgoing_box_id": null,
          "Item_Condition": "16",
          "Serial Number": null,
          "itemComment": null,
          "consignment": "0",
          "location": null,
          "merchant_sku": "testsku-125",
          "serviceLevel": "2",
          "product_id": "303342",
          "gproduct_name": "testsku-125",
          "product_detail_link": null,
          "parentASIN": null,
          "dimension_height": null,
          "dimension_length": null,
          "dimension_width": null,
          "dimension_unit": null,
          "weight": null,
          "shipping_weight": null,
          "weight_unit": null,
          "product_picture": null,
          "upc": null,
          "ean": null,
          "product_category": null,
          "marksman_category": "1",
          "List Price": "0",
          "New Price": "0",
          "Used Price": "0",
          "Brand": null,
          "Model": null,
          "Marksman_Category_Name": "Uncategorized",
          "Marksman_Category_Description": "Products that have no category assigned",
          "Marksman_Category_Dollars": null,
          "Marksman_Category_Cents": null,
          "inspection": "1",
          "fullService": "0",
          "Condition_Id": "16",
          "Condition_Name": "New",
          "Condition_Description": "A brand-new, unused, unopened item in its original packaging, with all original packaging materials",
          "Condition_Final": "1",
          "Approval_Required": "0",
          "consignment_modifier": "0.00",
          "discount": "0.00",
          "Sale Price": "0.00",
          "SKU": null
    	}
    	....
        ]
     },
     

```


You will notice the response is generally the same as the **getRequest** call. If any errors are encountered your request will *not* be created, and the **errors** response section will have a description of the problem.

If you attempt to create an outbound shipment which includes items unavailable for shipping you will get a response like this:

```json
"status": "FAILURE",
"apiVersion": "0.2",
"timestamp": "2017-01-25T14:19:49-05:00",
"mrscAccessCode": "20025",
"success": false,
"message": null,
"error": "Item not found: SKU testsku-125 not found.",
"query": "\/api.php\/?section=request&action=createShipment&comment=Please+place+fliers+with+my+logo+inside+all+packages.+Testing+update.&mrscAccessCode=20025&timestamp=1485371989&signature=c318e4ff06998417449c24277fc195149f7ace2d0777fd073f7252255be79d92",
"method": "POST"
```
## shipping

Shipping allows you to get shipping rates, purchase shipping labels, and check status of packages. We use GoShippo as our backend provider for labels. Using our shipping API you can take advantage of our discounted FedEx and USPS rates.



### getRates

Call this to set up a shipment and get available rates.

**Example Request:**

```json
"destination_address": {
    "name": "Test",
    "street_address1": "1726 Viking Avenue",
    "street_address2": " ",
    "city": "Orrville",
    "province": "OH",
    "postal_code": 44667,
    "country": "US",
    "email": null,
    "phone": "513-771-8777"
},
"from_address": {
    "name": "Marksman 20015",
    "street_address1": "571 Northland Blvd",
    "street_address2": " ",
    "city": "Cincinnati",
    "province": "OH",
    "postal_code": 45240,
    "country": "US",
    "email": null,
    "phone": "513-771-8777"
},
"insurance_required": false,
"insurance_amount": 0,
"signature_required": "no",
"saturday_delivery": false,
"packages": [
    {
        "length": 4,
        "height": 4.5,
        "width": 8,
        "weight": 2,
        "distance_unit": "in",
        "mass_unit": "lb"
    }
],
"testMode": false,
"mrscAccessCode": "20025",
"uri": "\/?section=shipping&action=getRates",
"debug": true
```
#### Parameters:

**destination_address** and **from_address**

Both of these parameters are required and require the same data. None of these fields can be left blank (this is a temporary limitation on our backend).

The fields should be self-explanatory aside from **email** and **phone.** You must provide an email address and a phone number to contact in the event of delivery problems. This is for the carrier to call you our your recipient about delivery issues.

If you do not provide this information it will be auto-completed using the email address and phone number you have on file with us. You can locate and update this information on our website by going to Your Account -> Update Your Account.

**insurance_required** (Boolean; true or false)

Select whether or not you need to purchase insurance for this shipment.

**insurance_amount** (float)

Set to the amount of insurance for the shipment if **insurance_required**. This is a float in USD.

**signature_required** (ENUM: "no", "standard", "adult"; default to "no")

Whether or not your require your package to be signed for.

*no:* No signature will be required

*standard*: A signature will be required

*adult*: Carrier will verify person who signs for package is an adult

**saturday_delivery**: (Boolean; true of false)

Whether or not your package can be delivered on a saturday. Defaults to false.

**packages** (array)

Packages is an array of packages included in this shipment. Each package must have the following information:

        "length": 4,
        "height": 4.5,
        "width": 8,
        "weight": 2,
        "distance_unit": "in",
        "mass_unit": "lb"
*length, height, width, and weight* (float)

*distance_unit*

Defaults to "in" for inches. Allowed values:

* in
* cm
* ft
* mm
* m
* yd

*mass_unit*

Defaults to "lb". Allowed values:

- lb
- g
- oz
- kg



A successful response will look as follows:

**Example Response:**

```json
"status": "SUCCESS",
"apiVersion": "0.2",
"timestamp": "2017-01-23T11:00:41-05:00",
"mrscAccessCode": "20025",
"success": true,
"message": [
    [
        {
            "object_state": "VALID",
            "object_id": "029d4a0e4f21493e9263c3d6652001c8",
            "provider": "USPS",
            "provider_image": "https:\/\/shippo-static.s3.amazonaws.com\/providers\/75\/USPS.png",
            "servicelevel_name": "Priority Mail Express",
            "days": 2,
            "arrives_by": null,
            "duration_terms": "Overnight delivery to most U.S. locations.",
            "amount": "25.94",
            "signature": "01b3955eb8514c93da0bd3a1d06f3ffa386445992cbdac3ea2efdbb8893e2f92",
            "shipment_id": 72
        },
        {
            "object_state": "VALID",
            "object_id": "dc1bb2435f5d40fd8725ed41ec51b992",
            "provider": "USPS",
            "provider_image": "https:\/\/shippo-static.s3.amazonaws.com\/providers\/75\/USPS.png",
            "servicelevel_name": "Priority Mail",
            "days": 2,
            "arrives_by": null,
            "duration_terms": "Delivery within 1, 2,\u00a0or 3 days\u00a0based on where your package started and where it\u2019s being sent.",
            "amount": "7.00",
            "signature": "ff7b152858605137e0b7d006f180285002afa544581359d9991f2c482c495362",
            "shipment_id": 72
        },
        {
            "object_state": "VALID",
            "object_id": "8e1c8680af294e22980148083bb44ec7",
            "provider": "USPS",
            "provider_image": "https:\/\/shippo-static.s3.amazonaws.com\/providers\/75\/USPS.png",
            "servicelevel_name": "Parcel Select",
            "days": 7,
            "arrives_by": null,
            "duration_terms": "Delivery in 2 to 8 days.",
            "amount": "7.26",
            "signature": "dde88bfd30e9015d2e42c6f700710e605b6cd5675345d95681706b8fdcb14f7e",
            "shipment_id": 72
        }
    ]
],
"error": null,
"query": "\/api.php\/?section=shipping&action=getRates&mrscAccessCode=20025&timestamp=1485187241&signature=a17d74cab59cd0a607f9f4f6814ce34433bccc8e1d19d52bc967c0efde78e056",
"method": "POST"
```
The **message** section of the response will contain an array of available shipping rates for each package in your request. The example above is for a shipment including only one package.

#### Rate Objects

There will be an array of rate objects for each package. If you have used goshippo before you may recognize these, as they are very similar.

You will purchase rates through our API by passing the rates you wish to purchase. You must not modify these objects.

**object_state**

Either "VALID" or "INVALID". A state of "INVALID" generally means the address information you've provided is not valid.

**provider**

This is the name of the shipping provider

**provider_image**

This is a url to an image or logo for the provider.

**servicelevel_name**

This is the provider's name for the type of service the rate will purchase, such as "Priority Mail", "First Class Mail", etc.

**days**

This is how long the provider estimates it will take to deliver your package at this rate.

**duration_terms**

This is a text explanation of the delivery terms for the rate

**amount**

This is how much the label will cost in USD.

**signature**

This is our internal signature of the rate.

**shipment_id**

This is used for our internal processes

### purchase

This action allows you to purchase a shipping rate you've received through **getRates**. You simply pass an array of rates you wish to purchase. You do not need to provide an address or other information, as this is stored via the **shipment_id** parameter.

**Optional Parameter**: request_id

If you pass request_id and it matches the internal id of an already existing OUTGOING request (created through the website or with **createShipment** the purchased label will be automatically attached to that request).

```json
"action": "purchase",
"mrscAccessCode": "20025",
"rates": [
    {       
        "object_id": "b1066812c6b341d8a56722b96358d6ae",
        "shipment_id": 74,
        "request_id": 1337
    }
]
```
A successful purchase will look like this:



```json
"status": "SUCCESS",
"apiVersion": "0.2",
"timestamp": "2017-01-23T11:49:38-05:00",
"mrscAccessCode": "20025",
"success": true,
"message": [
    {
        "status": "SUCCESS",
        "tracking_number": "9205590164917308211689",
        "total": "7.00",
        "file_id": 3195
    }
],
"error": null,
"query": "\/api.php\/?section=shipping&action=purchase&mrscAccessCode=20025&timestamp=1485190178&signature=fed372685c61ce8051d87304d1b2c9a27f5fb0433508d6eb4361b0da473fb492",
"method": "POST"
```
The message portion of the response will include an array of objects each detailing the label purchased. You will see the tracking number and total price, as well as a **file_id** field.

You can use the **file_id** to associate the shipping label with a request in our system, or download it through the **user** API section with the action **getFile**.

Your shipping label is also automatically saved under Your Account -> Your Files on our website. You can download it through your web browser.

### upsMiPurchase

Used to purchase UPS Mail Innovations in bulk. This will schedule a batch purchase of labels which can be downloaded at a later time.

Before using this API action it is important to understand the overall workflow:

1. You will send a purchase request using upsMiPurchase. This will schedule a batch label purchase in our system.
2. When you schedule a batch purchase our system will generate a "batch_id". This is the reference number for your purchase.
3. You will need to check our system periodically using checkShippingBatch. You will pass the batch_id.
4. checkShippingBatch will tell you if your batch is complete and if it contained any errors.
5. Once your batch is complete, you will be need to use getFile or getFileInline to download your labels.
6. The labels are provided in the format requested, inside of a zip file. The contained files will each be named using this convention:
   1. *package_id*_*tracking_number*.*format_extension*. For example, a label for package_id "678678676" in GIF format would be named 678678676_trackingNumberHere.gif

In general, our system can process about 2 UPS Mail Innovations per second. If you 

#### Parameters

upsMiPurchase takes the following parameters:

| Parameter | Required | Meaning                                                      |
| --------- | -------- | ------------------------------------------------------------ |
| mode      | No       | Either "test" or "purchase". Defaults to "test".             |
| format    | No       | One of "GIF", "EPL", or "ZPL". Defaults to "GIF".            |
| orders    | Yes      | Array of objects, each describing a shipment. See below for parameters. |

#### orders Parameters

Each member of "orders" uses the following fields:

| Field        | Type         | Meaning                                                      |
| ------------ | ------------ | ------------------------------------------------------------ |
| package_id   | string       | A unique reference number or order id for the shipment. If left blank our system generates one automatically. May not contain any spaces or special characters. |
| to_name      | string       | Name of the recipient                                        |
| to_addr1     | string       | Street address of recipient                                  |
| to_addr2     | string\|null | Apartment or building number                                 |
| to_city      | string       | Destination city                                             |
| to_state     | string       | 2-character alphabetically code for destination state. Example: OH for Ohio. See here for a list: https://www.ups.com/worldshiphelp/WS16/ENU/AppHelp/Codes/State_Province_Codes.htm |
| to_code      | string       | 5 digit "zip code" for destination                           |
| weight       | float        | Weight of package                                            |
| weight_unit  | enum         | Must be either "OZS" for ounces or "LBS" for pounds.         |
| length       | float        | Longest dimension of the package                             |
| width        | float        | Middle dimension of the package                              |
| height       | float        | Shortest dimension of the package                            |
| service      | enum         | Selects the type of service for the package. Valid codes are "M4" (for most packages), "M3" for Priority Mail, and "M2" for First Class Mail. |
| packing_type | enum         | Describes the characteristics of the package being shipped.  |
|              |              |                                                              |

#### Example

Here is an example of the workflow to purchase UPS Mail Innovations labels:

**Purchase Request:**

```json
{
    "mrscAccessCode": "99999",
    "body": {
        "section": "shipping",
        "action": "upsMiPurchase",
        "orders": [
            {
                "package_id": "45",
                "to_name": "Jason",
                "to_addr1": "1726 Viking Ave",
                "to_addr2": null,
                "to_city": "Orrville",
                "to_state": "OH",
                "to_code": "44667",
                "weight": "6",
                "weight_unit": "OZS",
                "length": 12,
                "width": 2,
                "height": 1,
                "service": "M4",
                "packing_type": "Irregulars"
            },
            {
                "package_id": "",
                "to_name": "Jason T",
                "to_addr1": "1726 Viking Ave",
                "to_addr2": null,
                "to_city": "Orrville",
                "to_state": "OH",
                "to_code": "44667",
                "weight": "6",
                "weight_unit": "OZS",
                "length": 12,
                "width": 2,
                "height": 1,
                "service": "M4",
                "packing_type": "Irregulars"
            }
        ]
    }
}
```

**Purchase Response:**

```json
{
    "status": "SUCCESS",
    "apiVersion": "0.2",
    "timestamp": "2018-07-16T10:12:51-04:00",
    "mrscAccessCode": "99999",
    "success": true,
    "message": {
        "batch_id": 15
    },
    "error": null,
    "query": "\/api.php?mrscAccessCode=99999&timestamp=1531750371&signature=73e97e25ee5880154cf54ad7ed7fa13350eaa744e518a3115afb2ce6c771c475",
    "method": "POST"
}
```

The batch_id will be used to check the status of the batch purchase, as shown below.

```json
{
    "mrscAccessCode": "99999",
    "body": {
        "section": "shipping",
        "action": "checkShippingBatch",
        "batch_id": 15
    }
}
```

**Response:**

```json
{
    "status": "SUCCESS",
    "apiVersion": "0.2",
    "timestamp": "2018-07-16T10:15:43-04:00",
    "mrscAccessCode": "99999",
    "success": true,
    "message": {
        "id": "15",
        "user_id": "1",
        "schedule_date": "2018-07-16 10:12:51",
        "zip_file_id": null,
        "spreadsheet_id": "15082",
        "mode": "test",
        "format": "GIF",
        "completed": false,
        "in_progress": false,
        "total_orders": "0",
        "total_errors": "0",
        "runtime": "0"
    },
    "error": null,
    "query": "\/api.php?mrscAccessCode=99999&timestamp=1531750543&signature=09156bbee1c5962e1f2e19bd8cf0415ef3f2d5527f6ba877bb887370cb489d60",
    "method": "POST"
}
```



##### UPS Mail Innovations Packing Types

The following packing types can be used with Mail Innovations. Selecting the correct packing type is very important, as selecting the wrong one may result in *very large* price differences and delivery times.

Please check these two links for detailed information:

http://www.upsmailinnovations.com/services/qualified.html

http://www.upsmailinnovations.com/pdfs/UPSMI_Qualifying_Mail_Page.pdf

| packing_type  | Min Weight | Max Weight | Description                                           |
| ------------- | ---------- | ---------- | ----------------------------------------------------- |
| First Class   |            |            | Same rules as USPS First Class. Must use service "M2" |
| Priority      |            |            | Same rules as USPS Priority. Must use service "M3"    |
| Machinables   | 6oz        | 15.99oz    |                                                       |
| Irregulars    | 1oz        | 15.99oz    |                                                       |
| Parcel Post   | 1lbs       | xxlbs      |                                                       |
| BPM Parcel    | 1lb        | 15lbs      |                                                       |
| Media Mail    | 1lb        | xxlbs      |                                                       |
| BPM Flat      | 1oz        | 15.99oz    |                                                       |
| Standard Flat | 1oz        | 15.99oz    |                                                       |

### checkShippingBatch

Use this API call to check the status of a batch label purchase and to download the labels. This is used with upsMuPurchase.

The only parameter is batch_id, which is the batch_id returned by upsMiPurchase.

| Field          | Meaning                                                      |
| -------------- | ------------------------------------------------------------ |
| id             | batch_id                                                     |
| user_id        | user_id of the user who scheduled the batch purchase.        |
| scheduled_date | Date and time (local to the API endpoint) the batch was scheduled. |
| zip_file_id    | Id of the zip file containing labels. Can be passed to the **user** action **getFile** or **getFileInline** to download. |
| spreadsheet_id | Id of the CSV file containing the shipments. Can be downloaded the same way as zip_file_id. Please note, if you placed your batch using the API our system will have created this file for you. |
| mode           | Either "test" or "purchase". If "test", this means the labels are test labels and cannot be used for actual shipping. |
| format         | One of "GIF", "EPL", or "ZPL", representing the format of the labels for this shipment. |
| completed      | True or false. Changes to true once the batch has completed processing. |
| in_progress    | True or false. Changes to true while the batch is being processed. Batches which remain "in_progress" for extended periods of time may indicate a system failure or serious errors in the shipments. |
| total_orders   | Total number of orders processed. Will be "0" until the batch has been processed. |
| total_errors   | Total number of shipments containing errors. This will be "0" until the batch has been processed. |
| runtime        | Total time in seconds spent processing batch.                |

Example Response:

```json
{
    "status": "SUCCESS",
    "apiVersion": "0.2",
    "timestamp": "2018-07-16T10:21:04-04:00",
    "mrscAccessCode": "99999",
    "success": true,
    "message": {
        "id": "15",
        "user_id": "1",
        "schedule_date": "2018-07-16 10:12:51",
        "zip_file_id": null,
        "spreadsheet_id": "15082",
        "mode": "test",
        "format": "GIF",
        "completed": false,
        "in_progress": false,
        "total_orders": "0",
        "total_errors": "0",
        "runtime": "0"
    },
    "error": null,
    "query": "\/api.php?mrscAccessCode=99999&timestamp=1531750864&signature=2f2b0cd3c29c032f20c2a9ccc796e8a8b3e034f151a711dc584c41a88c15d523",
    "method": "POST"
}
```



## user

User section allows access to various billing functionality, reports, uploaded files, and other things that don't directly fit in another section.

### ping

This action takes no parameters. It simply generates am empty response to verify the API endpoint is live and that you are successfully authenticated.

### basicInfo

Returns some basic info about an account, including contact information, balance, storage allocation, etc.

Example response:

```json
"message": {
        "accountNo": "99999",
        "email": "it@marksmanrsc.com",
        "first_name": "Jason",
        "last_name": "Thistlethwaite",
        "phone": null,
        "balance": "985.61",
        "maxLabelPrice": "4000",
        "warehouseFee": "45",
        "simpleRequests": true,
        "allocatedStorage": "20",
        "allocatedLongTermStorage": "5"
    }
```

| Field                    | Meaning                                                      |
| ------------------------ | ------------------------------------------------------------ |
| accountNo                | Account number of the account                                |
| email                    | Primary contact email for the account                        |
| first_name               | First name of primary account holder                         |
| last_name                | Last name of primary account holder                          |
| phone                    | Phone number of primary account holder                       |
| balance                  | Current deposit balance in USD                               |
| maxLabelPrice            | Maximum price of a single shipping label customer can purchase, expressed in cents (USD) |
| warehouseFee             | Standard monthly storage fee for customer (by CuFT) expressed in cents USD. |
| simpleRequests           | Boolean **true** or **false**; whether or not Simple Requests is activated for the customer. |
| allocatedStorage         | Dedicated short-term storage space allocated to customer, measured in cubic feet |
| allocatedLongTermStorage | Dedicated long-term storage space allocated to customer, measured in cubic feet. The definition of "long term storage" changes periodically with business needs, but is typically defined as anything stored for more than 90 days. |



### billUser [internal]

This is an internal API function used to apply fees or refunds to accounts.

### getFile

Download a file just as a browser would (server side will set file name, size, and type through HTTP headers).

The only parameter this action takes is **file_id**, which should be the database id of the file as referenced in other API sections (for example ajax2 -> getRequestList attachment_id).

### getFileInline

Works the same way as getFile, except the file is base64-encoded and returned as a string in a response like this:

```json
message: {
    [
        "filename": "name of the file",
        "size": 900012,
        "type": "image/jpeg",
        "base64": "....base64 encoded file..."
    ]
}
```

Future plans include adding support for multiple file_id's to download multiple files at once.

## ajax2

This is a newer section to our API that's used for many of the interactive displays on our website. You can access this API section for various reports and information listings.

### Special Parameters

This section of the API allows for some special parameters for filtering and sorting results. They work as follows:

| Parameter | Type                      | Description                                                  |
| --------- | ------------------------- | ------------------------------------------------------------ |
| page      | int                       | Which page of results to display                             |
| pageSize  | int / mixed               | Number of results to return per page. Special value "all"  is allowed, but excessive usage may result in throttling. |
| filter    | array (numerical indexed) | filter returned results by column                            |

### Response Format

The response format of this section varies slightly from the other API sections. The same "envelope" is used, but the message will be structured like this:

```json
"message": {
        "total_rows": "2",
        "columns": [
        ],
        "rows": [
            {
            },
            {
            }
        ],
        "query_time": 0.177531,
        "count_time": 0.079640
    }
```

| Section    | Meaning                                                      |
| ---------- | ------------------------------------------------------------ |
| total_rows | The total number of available results                        |
| columns    | An array of "display names" for the columns in each record. This is what the name of the column would be if displayed in a table on our website. **This is also the list of columns to which you can apply filters.** |
| rows       | An array of objects, with each object being a record matching your query |
| query_time | Total time (in seconds) it took to execute your query        |
| count_time | Total time (in seconds) it took to total the number of results available |

### Using Filters

Filters are a numerically indexed array of fuzzy matches to apply to columns. The numerical index is based on the column's position in the "columns" section of the API response.

This is an example of how they work:

```JSON
{
    "mrscAccessCode": "99999",
    "body": {
        "section": "ajax2",
        "action": "getRequestList",
        "pageSize": 10,
        "filter": {
            "0": "916-125",
            "2": "2017"
        }
    }
}
```

The above uses the getRequestList action to list requests which contain "916-125" in the Order Id and were last updated in 2017.

This example searches for requests including the tracking number "1Z8921550309768308":

```JSON
{    
    "mrscAccessCode": "99999",
    "body": {
        "section": "ajax2",
        "action": "getRequestList",
        "pageSize": 1,
        "filter": {
            "9": "1Z8921550309768308"
        }
    }
}
```

### Actions

#### getRequestList - Get list of all requests for your account

##### Filterable Columns:

| Column Index | Name                | Type of Information                                          |
| ------------ | ------------------- | ------------------------------------------------------------ |
| 0            | Order Id            | Order Id of the request                                      |
| 1            | Order Placed        | Creation date of the order in YYYY-MM-DD hh:mm:ss format     |
| 2            | Last Updated        | Date and time status, comment, or item quantity of the request was changed in YYYY-MM-DD hh:mm:ss format. |
| 3            | Request Type        | The type of request. See the section on Request Types for more information |
| 4            | Request Status      | The status of the request. See the section on Request Status for more information |
| 5            | Account No          | Account No of the user who owns/created the request.         |
| 6            | Items Processing    | The meaning of this can change based on the Request Type. For OUTGOING requests, this is the number of items which have been processed and packed. For any other type of request this is how many items were actually marked received. |
| 7            | Total Items         | Total number of items (expected) to be in the request.       |
| 8            | Notes from Customer | Textual notes about the request, provided by Customer when the request was placed. |
| 9            | Notes from Marksman | Textual notes about the request, provided by Marksman        |
| 10           | Packages            | A comma-delimited list of tracking numbers associated with the request |
| 11           | File Attachments    | A comma-delimited list of any file attachments associated with the request |

##### Row Data

The **rows** section of the response will contain an array of objects representing each of the above columns. There are also 3 other fields returned:

| Field          | Description                                                  |
| -------------- | ------------------------------------------------------------ |
| id             | The internal database id of the request. This can be used to reference the request in other sections of the API. |
| attachment_ids | A comma-delimited list of the database id's of attachments associated with the request. These id's can be used to download or reference the attachments in other sections of the API. |
| simple_request | Either "0" or "1", indicating whether the request was created as a Simple Request. |

##### Special Subsets

You can request a few special subsets of requests from getRequestList by specifying the parameter "specialType" in the request.

| Special Type | Purpose                                                      |
| ------------ | ------------------------------------------------------------ |
| INCOMING     | Will display only requests sent to Marksman which are not in an error status. |
| OUTBOUND     | Will display requests shipped or shipping from Marksman      |
| ABNORMAL     | Requests where some abnormal situation has occurred which may require human attention. |
| STUCK        | Requests in the process of shipping from Marksman which cannot be completed for some reason. Generally, the "Notes from Marksman" will detail the reason. |
| AUTOREQUEST  | Requests received as "AUTOREQUEST" which still have not been resolved. |
| SIMPLE       | Simple Requests which have not yet been processed. For requests of type "PREP" no customer-action is required. Other request types may require customer-action. |

#### getRequestCounts

Returns a list of each of the "specialTypes" used within getRequestList and the number of requests of each type. Example:

```json
"message": {
        "INCOMING": 5496,
        "OUTBOUND": 8150,
        "ABNORMAL": 1940,
        "STUCK": 3,
        "AUTOREQUEST": 413,
        "SIMPLE": 4,
        "ALL": 15484
    }
```

#### agingInventory -- Display inventory in stock for more than a period of time

This action provides an overview of inventory based on the space it occupies in our warehouse and how long it has been stored with us. By default, this action lists inventory which has been in storage for 3 or more months (but this is configurable; see the Months Parameter below).

##### Filterable Columns:

| Column Index | Name              | Type of Information                                          |
| ------------ | ----------------- | ------------------------------------------------------------ |
| 0            | Checkbox          | Has no use in context of the API                             |
| 1            | Account No        | Account who owns the product                                 |
| 2            | ASIN              | ASIN of the product                                          |
| 3            | SKU               | Merchant-provided SKU for product (where available)          |
| 4            | Title             | Title of the product (as would be displayed on Amazon or eBay) |
| 5            | Condition         | Textual description of product condition                     |
| 6            | Category          | Marksman category for product (used for billing purposes for returns) |
| 7            | Quantity          | Quantity of the item in storage                              |
| 8            | Avg Months Stored | How many months, on average, this item in this condition is stored before leaving Marksman. |
| 9            | Min Months        | Minimum number of months any current unit of this stock has been stored. |
| 10           | Max Months        | Max number of months any current unit of this stock has been stored. |
| 11           | CuFt              | Total Cubic Feet of storage place occupied by this stock.    |
| 12           | Details           | Unused by the API.                                           |

##### Row Data

Three additional fields are returned with each result:

| Field        | Meaning                                          |
| ------------ | ------------------------------------------------ |
| product_id   | Database id of the product template.             |
| condition_id | Numeric representation of item condition.        |
| user_id      | Database id of the customer who owns the product |



##### Example Response:

```json
[
    {
                "Checkbox": "0",
                "Account No": "99999",
                "ASIN": "B00SXX975K",
                "SKU": "",
                "Title": "Toshiba Satellite C55D-B5308 15.6-Inch Laptop (AMD E1-Series, 4GB Memory, 500GB Hard Drive) Jet Black",
                "Condition": "Used - Very Good",
                "Category": "Laptops",
                "Quantity": "1",
                "Avg Months Stored": "29.0",
                "Min Months": "29",
                "Max Months": "29",
                "CuFt": "0.30",
                "product_id": "787",
                "condition_id": "14",
                "user_id": "3",
                "Details": "0"
            }
]
```

##### Months Parameter

The special parameters "months" can be passed in the request body to adjust the behavior of this action. Setting a value of 0 should display all finalized/shippable inventory in storage.





# Appendix

## Pre-Authorized Repairs

Pre-authorized repairs specifies an additional budget for improving item condition which may be used if the condition of an item can be significantly improved with additional work outside the normal services provided.

This optional service is highly recommended for customers with high volumes of returns because it can improve the speed of service and reduce unnecessary communication.

An example of this might be a returned item which is very dirty. Normally Marksman would indicate the item is dirty and wait on customer approval before cleaning the item. With a pre-authorized repair of 5 dollars Marksman would evaluate if the item can be cleaned for 5 dollars or less, and then act accordingly without further confirmation.



