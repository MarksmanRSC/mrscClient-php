<?php
namespace mrscClient;
require_once '../src/autoload.php';


$client = new mrscClient();
$client->body = [ "section" => "user", "action" => "ping" ];
$client->send();
