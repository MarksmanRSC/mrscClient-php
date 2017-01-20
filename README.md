# mrscClient-php

A PHP client for the Marksman RSC API. Marksman RSC is a leading reverse logistics provider for ecommerce sellers. 

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

Each request MUST have the following URL parameters:

**mrscAccessCode** - This is the Access Code given to you on our website.

**timestamp** - The current UNIX timestamp at the time you created your request.*

**signature** - This is a sha256 hmac of the URI used in your request, using your secretKey as the key.

**:  It is important to make sure your clock is accurate, or your API requests may be rejected.*

Example:

`$uri = '/api.php?mrscAccessCode=111777&timestamp=1484865559';`

`$signature = hash_hmac('sha256',  ​$uri, $secretKey);`

API requests should be POST requests with Content-Type: application/json.

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
### Common Reponse Fields & Meanings:

#### **status	**  

​	textual indication of whether your request succeeded or failed

#### **apiVersion	**

​	The version of the API you are communicating with. At this time, there is only one version

#### **timestamp	**

​	The time your request was received and processed on our end.

#### **mrscAccessCode	**

​	This is mrscAccessCode of the user whom you are authenticated as.

At this time there is no direct use for this, but in the future users will be able to grant developers access to their accounts, similar to how Amazon's MWS API allows.

#### **success**

​	Will be boolean **true** or **false**, indicating the success of your request.

#### **message**

​	This will contain either text or a JSON object, depending on what API section and action you requested.

​	This is the field typically populated by query results.

#### **error**

​	If an error is encountered a textual description of the error will be indicated here.

#### **query**

​	This is a copy of your request URI and query string for reference purposes

#### **method**

​	This is the HTTP request method used for your request

# API Sections

Our API is divided into several sections, as follows:

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

   "status": "SUCCESS",

```json
"apiVersion": "0.2",
"timestamp": "2017-01-20T16:40:59-05:00",
"mrscAccessCode": "20025",
"success": true,
"message": {
    "id": "3078",
    "company_id": "0",
    "user_id": "62",
    "request_date": "2017-01-15 13:23:26",
    "modify_date": "2017-01-15 13:23:26",
    "order_id": "170115STL",
    "request_status": "PENDING",
    "return_reason": null,
    "request_type": "FBA",
    "items_received": "0",
    "items_total": "1",
    "fee_dollars": "0",
    "fee_cents": "0",
    "comment": null,
    "customer_notes": null,
    "marksman_ships": false,
    "items": [
        {
            "iproduct_id": "11628",
            "ASIN": "B01DSSLJ3M",
            "FNSKU": "X0019TFNPT",
            "iproduct_name": "MSI GP72 Leopard Pro-495 17.3\\\" GAMING LAPTOP NOTEBOOK GTX960M i7-6700HQ 16GB 256GB M.2 SATA WINDOWS 10 USB TYPE-C",
            "request_id": "3078",
            "outgoing_request_id": null,
            "itemNo": null,
            "company_id": null,
            "user_id": "62",
            "checkin_date": null,
            "box": null,
            "outgoing_box_id": null,
            "Item_Condition": "1",
            "Serial Number": null,
            "itemComment": null,
            "consignment": "0",
            "location": null,
            "merchant_sku": "JS-FQMV-2CEE",
            "serviceLevel": "2",
            "product_id": "303229",
            "gproduct_name": "MSI GP72 Leopard Pro-495 17.3\\\" GAMING LAPTOP NOTEBOOK GTX960M i7-6700HQ 16GB 256GB M.2 SATA WINDOWS 10 USB TYPE-C",
            "product_detail_link": "https:\/\/www.amazon.com\/MSI-GP72-Leopard-NOTEBOOK-i7-6700HQ\/dp\/B01DSSLJ3M%3Fpsc%3D1%26SubscriptionId%3DAKIAIKRYDR3R75D2V3DQ%26tag%3Dcropcroprole%26linkCode%3Dxm2%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB01DSSLJ3M",
            "parentASIN": "B01HTBB7XQ",
            "dimension_height": "3.7",
            "dimension_length": "23",
            "dimension_width": "15",
            "dimension_unit": "INCHES",
            "weight": null,
            "shipping_weight": "10.5",
            "weight_unit": "POUNDS",
            "product_picture": "https:\/\/images-na.ssl-images-amazon.com\/images\/I\/418GuWn6IGL._SL75_.jpg",
            "upc": "824142128633",
            "ean": "0824142128633",
            "product_category": "14806",
            "marksman_category": "1",
            "List Price": "0",
            "New Price": "117517",
            "Used Price": "0",
            "Brand": "MSI",
            "Model": "GP72 LEOPARD PRO-495",
            "Marksman_Category_Name": "Uncategorized",
            "Marksman_Category_Description": "Products that have no category assigned",
            "Marksman_Category_Dollars": null,
            "Marksman_Category_Cents": null,
            "inspection": "1",
            "fullService": "0",
            "Condition_Id": "1",
            "Condition_Name": "Refurb Pending",
            "Condition_Description": "Product has not yet begun processing. It may still be in the mail to Marksman",
            "Condition_Final": "0",
            "Approval_Required": "1",
            "consignment_modifier": "0.00",
            "discount": "0.00",
            "Sale Price": "0.00",
            "SKU": null
        }
    ]
},
"error": null,
"query": "\/api.php?mrscAccessCode=20025&timestamp=1484948459&signature=ecd6c0ddf293be9b47fbf63cf6485d1c2fceaef22832ede43088a98377a4b7cf",
"method": "POST"
```
## Shipping

Shipping allows you to get shipping rates, purchase shipping labels, and check status of packages.

## User

User allows you to pull various reports, such as financial/billing statements about your account.