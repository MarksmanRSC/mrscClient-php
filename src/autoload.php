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

function autoload($class_name)
{
    echo "Looking for $class_name\n";
    $parts = str_replace('\\', '/', $class_name);
    
    $dir = dirname(__FILE__). '/';
    
    if (is_file($dir . $parts . '.php')) {
        include_once $dir . $parts . '.php';
    }
}


spl_autoload_register("mrscClient\autoload");