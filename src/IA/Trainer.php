<?php

namespace Jarenal\IA;

use DI\Annotation\Inject;

class Trainer
{
    private $games = TRAINING_GAMES;
    private $player1 = 'O';
    private $player2 = 'X';
    private $counters = ['X'=>0, 'O'=>0, 'T'=>0];
    private $report = ['X'=>0, 'O'=>0, 'T'=>0];

    /**
     * @Inject("Jarenal\IA\IA")
     */
    private $ia;

    /**
     * @Inject("Jarenal\Model\Game")
     */
    private $game;

    public function start()
    {
        for ($i=0; $i < $this->games; $i++) {

            if ($i % 10000 == 0) {
                $this->ia->save();
            }

            $boardState = [['','',''],['','',''],['','','']];

            do {
                $coords1 = $this->game->makeMove($boardState, $this->player2, false);

                if($coords1) {
                    $boardState[$coords1[1]][$coords1[0]] = $this->player1;
                }

                echo "\n".$this->convertState2Hash($boardState);

                $winner = $this->game->findWinner($boardState);
                if ($winner) {
                    break;
                }

                if (count($this->ia->findFreeCoordinates($boardState))) {
                    $coordinates = $this->game->makeMove($boardState, $this->player1, false);
                    $coordinates = [$coordinates[1], $coordinates[0]];

                    $this->ia->analyzePosition($boardState, $coordinates, $this->player2);
                    $boardState[$coordinates[0]][$coordinates[1]] = $this->player2;
                }

                echo "\n".$this->convertState2Hash($boardState);
                $winner = $this->game->findWinner($boardState);
            } while ($winner === false);

            echo "\nThe winner is $winner";
            $this->counters[$winner]++;

            if ($this->player2 == $winner) {
                $this->ia->updateQTable();
            } else {
                $this->ia->resetStates();
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
