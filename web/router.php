<?php

require_once __DIR__."/../config/config.php";

$routes = [];
$routes["^/$"] = ["class" => "Jarenal\Controller\HomeController", "action" => "index"];
$routes["^/api/move$"] = ["class" => "Jarenal\Controller\ApiController", "action" => "move"];

$fullpath = dirname(__FILE__) . $_SERVER['REQUEST_URI'];

foreach ($routes as $regex => $module) {
    if (preg_match('%'.$regex.'%', $_SERVER['REQUEST_URI'])) {
        $controller = $container->get($module['class']);
        die($controller->{$module['action']}());
    }
}

// if requested file is'nt a php file
if (!preg_match('/\.php$/', $fullpath)) {
    return false;
}
