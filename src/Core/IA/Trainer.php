<?php

namespace Jarenal\Core\IA;

use Jarenal\Core\Container;

class Trainer
{
    private $games = 100000;
    private $player1 = 'O';
    private $player2 = 'X';
    private $counters = ['X'=>0, 'O'=>0, 'T'=>0];
    private $report = ['X'=>0, 'O'=>0, 'T'=>0];
    private $container;

    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    public function start()
    {
        $IA = $this->container->get('IA');
        $game = $this->container->get('game');
        for ($i=0; $i < $this->games; $i++) {

            if ($i % 10000 == 0) {
                $IA->save();
            }

            $boardState = [['','',''],['','',''],['','','']];

            do {
                $coords1 = $game->makeMove($boardState, $this->player2, false, true);

                if($coords1) {
                    $boardState[$coords1[1]][$coords1[0]] = $this->player1;
                }

                echo "\n".$this->convertState2Hash($boardState);

                $winner = $game->findWinner($boardState);
                if ($winner) {
                    break;
                }

                if (count($IA->findFreeCoordinates($boardState))) {
                    $coordinates = $game->makeMove($boardState, $this->player1, false, true);
                    $coordinates = [$coordinates[1], $coordinates[0]];

                    $IA->analyzePosition($boardState, $coordinates, $this->player2);
                    $boardState[$coordinates[0]][$coordinates[1]] = $this->player2;
                }

                echo "\n".$this->convertState2Hash($boardState);
                $winner = $game->findWinner($boardState);
            } while ($winner === false);

            echo "\nThe winner is $winner";
            $this->counters[$winner]++;

            if ($this->player2 == $winner) {
                $IA->updateQTable();
            } else {
                $IA->resetStates();
            }
        }

        $this->report['X'] = ($this->counters['X']*100/$this->games);
        $this->report['O'] = ($this->counters['O']*100/$this->games);
        $this->report['T'] = ($this->counters['T']*100/$this->games);
        $this->report['games'] = $this->games;
        return $this->report;
    }

    public function convertState2Hash(array $state)
    {
        $flat_state = [];

        foreach ($state as $row) {
            foreach ($row as $value) {
                if ($value) {
                    $flat_state[] = $value;
                } else {
                    $flat_state[] = '-';
                }
            }
        }

        return strtolower(implode($flat_state, ""));
    }
}
