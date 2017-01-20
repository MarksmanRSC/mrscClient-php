<?php

require_once '../src/autoload.php';

$file = mrscClient\mrscClient::getFile(3194);

file_put_contents("/tmp/test.pdf", $file);
