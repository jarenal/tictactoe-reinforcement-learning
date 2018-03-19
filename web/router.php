<?php

require_once __DIR__."/../vendor/autoload.php";

$routes = [];
$routes["^/$"] = ["class" => "Jarenal\Core\Controller\HomeController", "action" => "index"];
$routes["^/api/move$"] = ["class" => "Jarenal\Core\Controller\ApiController", "action" => "move"];

$fullpath = dirname(__FILE__) . $_SERVER['REQUEST_URI'];

foreach ($routes as $regex => $module) {
    if (preg_match('%'.$regex.'%', $_SERVER['REQUEST_URI'])) {
        $controller = new $module['class'];
        die($controller->{$module['action']}());
    }
}

// if requested file is'nt a php file
if (!preg_match('/\.php$/', $fullpath)) {
    //die(mime_content_type($fullpath));
    //header('Content-Type: '.mime_content_type($fullpath));
    //$fh = fopen($fullpath, 'r');
    //fpassthru($fh);
    //fclose($fh);
    return false;
}
