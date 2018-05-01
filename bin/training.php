<?php

require_once "../config/config.php";

use Jarenal\IA\Trainer;

$trainer = $container->get(\Jarenal\IA\Trainer::class);
$report = $trainer->start();

echo "\nGames played: {$report['games']}";
echo "\nX = {$report['X']}% | O = {$report['O']}% | T = {$report['T']}%";
