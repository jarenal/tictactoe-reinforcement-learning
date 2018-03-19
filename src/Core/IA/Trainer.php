<?php

namespace Jarenal\Core\IA;

use Jarenal\Core\Model\Game;

class Trainer
{
    private $qTablePath;
    private $games = 100000;
    private $gamma;
    private $player1 = 'O';
    private $player2 = 'X';
    private $counters = ['X'=>0, 'O'=>0, 'T'=>0];
    private $report = ['X'=>0, 'O'=>0, 'T'=>0];

    public function __construct(string $qTablePath, $gamma)
    {
        $this->qTablePath = $qTablePath;
        $this->gamma = $gamma;
    }

    public function start()
    {
        $IA = IA::getInstance($this->qTablePath, $this->gamma);
        $game = Game::getInstance();
        for ($i=0; $i < $this->games; $i++) {
            $boardState = [['','',''],['','',''],['','','']];
            $free = false;
            do {
                $counter = 0;

                do {
                    $coordinates = [rand(0, 2), rand(0, 2)];
                    if (empty($boardState[$coordinates[0]][$coordinates[1]])) {
                        $free = true;
                    } else {
                        $counter++;
                    }
                } while ($free === false && $counter < 100);

                $boardState[$coordinates[0]][$coordinates[1]] = $this->player1;

                if (count($IA->findFreeCoordinates($boardState))) {

                    $coordinates = $game->makeMove($boardState, $this->player1, true, $this->qTablePath, true);
                    $coordinates = [$coordinates[1], $coordinates[0]];
                    $IA->analyzePosition($boardState, $coordinates, $this->player2);
                    $boardState[$coordinates[0]][$coordinates[1]] = $this->player2;
                }

                echo "\n".$this->convertState2Hash($boardState);
                $winner = $game->findWinner($boardState);
            } while ($winner === false);

            echo "\nThe winner is $winner";
            $this->counters[$winner]++;

            if ($this->player2 == $winner || $winner == 'T') {
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
