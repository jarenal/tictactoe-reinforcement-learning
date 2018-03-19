<?php

require_once __DIR__."/../vendor/autoload.php";

if ($_GET['c'] == "api") {
    $class = "Jarenal\Core\Controller\ApiController";
} else {
    $class = "Jarenal\Core\Controller\HomeController";
}

$app  = new $class;
die($app->{$_GET['a']}());