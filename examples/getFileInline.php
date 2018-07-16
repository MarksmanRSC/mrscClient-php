<?php
namespace mrscClient;
require_once '../src/autoload.php';


$client = new mrscClient();
$client->body = [ 
	"section" => "user",
  "action" => "getFileInline",
	"file_id" => 15081
];
$client->send();
