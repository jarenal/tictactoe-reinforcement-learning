<?php

require_once "../vendor/autoload.php";

use Jarenal\Core\IA\Trainer;

$trainer = new Trainer();
$report = $trainer->start();

echo "\nGames played: {$report['games']}";
echo "\nX = {$report['X']}% | O = {$report['O']}% | T = {$report['T']}%";
