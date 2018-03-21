<?php

namespace Jarenal\Core\Model;

use Jarenal\Core\Container;

class Game implements MoveInterface
{
    private $container;
    private static $instance;

    public static function getInstance()
    {
        if(!isset(self::$instance)) {
            $className = get_called_class();
            self::$instance = new $className();
        }

        return self::$instance;
    }

    private function __clone()
    {
        // Forbidden
    }

    private function __wakeup()
    {
        // Forbidden
    }

    public function makeMove($boardState, $playerUnit, $useIA = false, $training=false)
    {
        $this->container = Container::getInstance();

        $tmp = [];
        $IA = $this->container->get('IA');
        foreach ($boardState as $y => $row) {
            foreach ($row as $x => $cell) {
                $tmp[$y][$x] = trim($cell, '-');
            }
        }
        $boardState = $tmp;

        if ($useIA && $training===false) {
            $freePositions = $IA->findFreeCoordinates($boardState);
            $cpuPlayer = $playerUnit == 'X' ? 'O' : 'X';
            $ratings = [];
            foreach ($freePositions as $key => $coords) {
                $ratings[$key] = $IA->getRatingFromQTable($boardState, $coords, $cpuPlayer);
            }
            $maxRatingKey = false;
            $maxRate = 0;
            foreach ($ratings as $keyRate => $rate) {
                if ($rate > $maxRate) {
                    $maxRate = $rate;
                    $maxRatingKey = $keyRate;
                }
            }

            if ($maxRatingKey !== false) {
                $coords = $freePositions[$maxRatingKey];
            } else {
                $randKey = rand(0, count($freePositions) -1);
                $coords = $freePositions[$randKey];
            }

            // Invert coordinates for to use X-Y format
            return [$coords[1], $coords[0]];
        } else {
            $free = false;
            $counter = 0;

            do {
                $coords = [rand(0, 2), rand(0, 2)];

                if (empty($boardState[$coords[0]][$coords[1]])) {
                    $free = true;
                } else {
                    $counter++;
                }
            } while ($free === false && $counter < 100);

            if ($free) {
                // Invert coordinates for to use X-Y format
                return [$coords[1], $coords[0]];
            } else {
                return [];
            }
        }
    }

    public function findWinner($boardState)
    {
        $lines = [];
        $lines[] = $boardState[0];
        $lines[] = $boardState[1];
        $lines[] = $boardState[2];
        $lines[] = [$boardState[0][0], $boardState[1][0], $boardState[2][0]];
        $lines[] = [$boardState[0][1], $boardState[1][1], $boardState[2][1]];
        $lines[] = [$boardState[0][2], $boardState[1][2], $boardState[2][2]];
        $lines[] = [$boardState[0][0], $boardState[1][1], $boardState[2][2]];
        $lines[] = [$boardState[0][2], $boardState[1][1], $boardState[2][0]];

        foreach ($lines as $line) {
            $playerX = 0;
            $playerO = 0;

            foreach ($line as $square) {
                if ($square == 'X') {
                    $playerX++;
                } elseif ($square == 'O') {
                    $playerO++;
                }
            }

            if ($playerX == 3) {
                return 'X';
            }

            if ($playerO == 3) {
                return 'O';
            }
        }

        $freeCells = 0;

        foreach ($boardState as $row) {
            foreach ($row as $cell) {
                if (empty($cell)) {
                    $freeCells++;
                }
            }
        }

        if (!$freeCells) {
            return 'T';
        }

        return false;
    }
}
