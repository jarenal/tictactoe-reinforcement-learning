<?php

require_once __DIR__."/../vendor/autoload.php";

define('TRAINING_GAMES', 900000);

$builder = new DI\ContainerBuilder();
$builder->useAnnotations(true);
$builder->addDefinitions([
    Jarenal\IA\IA::class => DI\factory(function () {
        $ia = new \Jarenal\IA\IA(__DIR__ . "/../database/q_table.csv", 0.8);
        return $ia;
    })
]);
$container = $builder->build();