<?php

require_once __DIR__."/../config/config.php";

if ($_GET['c'] == "api") {
    $controller = $container->get(\Jarenal\Controller\ApiController::class);
} else {
    $controller = $container->get(\Jarenal\Controller\HomeController::class);
}

die($controller->{$_GET['a']}());