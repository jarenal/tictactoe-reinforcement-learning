<?php
namespace Jarenal\Core\IA;


class IA
{
    private $gamma;
    private $qTable = [];
    private $qTablePath;
    private static $instance;
    private $states = [];

    protected function __construct($qTablePath, $gamma)
    {
        $this->qTablePath = $qTablePath;

        // Read Q-Table from file
        if (file_exists($this->qTablePath)) {
            if (($handle = fopen($this->qTablePath, 'r')) !== false) {
                while (($data = fgetcsv($handle, 1000))) {
                    $hash = array_shift($data);
                    $this->qTable[$hash] = $data;
                }
                fclose($handle);
            }
        }

        $this->gamma = $gamma;
    }

    public static function getInstance($qTablePath, $gamma)
    {
        if(!isset(self::$instance)) {
            $className = get_called_class();
            self::$instance = new $className($qTablePath, $gamma);
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

    public function __destruct()
    {
        $this->save();
    }

    public function save()
    {
        $fp = fopen($this->qTablePath, 'w');

        foreach ($this->qTable as $hash => $actions) {
            fputcsv($fp, array_merge([$hash], $actions));
        }

        fclose($fp);
    }

    public function setQTable(array $qTable)
    {
        $this->qTable = $qTable;
    }

    private function convertState2Hash(array $state, string $cpuPlayer)
    {
        $flat_state = [];

        foreach ($state as $row) {
            foreach ($row as $value) {
                if ($value) {
                    if ($cpuPlayer == 'X') {
                        $flat_state[] = $value=='O' ? 'O' : $value;
                    } else {
                        $flat_state[] = $value=='X' ? 'O' : 'X';
                    }
                } else {
                    $flat_state[] = '-';
                }
            }
        }

        return strtolower(implode($flat_state, ""));
    }

    public function getReward(array $state, array $coordinates, string $cpuPlayer)
    {
        $newState = $state;
        $newState[$coordinates[0]][$coordinates[1]] = $cpuPlayer;
        return $this->ratePosition($newState, $cpuPlayer);
    }

    public function getRatingFromQTable(array $state, array $coordinates, string $cpuPlayer)
    {
        $action = $this->convertCoordinatesToAction($coordinates);
        $hash = $this->convertState2Hash($state, $cpuPlayer);

        if (array_key_exists($hash, $this->qTable)) {
            return $this->qTable[$hash][$action];
        } else {
            return 0;
        }
    }

    public function addState(array $state, array $coordinates, int $reward, string $cpuPlayer)
    {
        $this->states[] = ['state' => $state, 'coordinates' => $coordinates, 'reward' => $reward, 'cpuPlayer' => $cpuPlayer];
    }

    public function updateQTable()
    {
        foreach ($this->states as $item)
        {
            $action = $this->convertCoordinatesToAction($item['coordinates']);
            $hash = $this->convertState2Hash($item['state'], $item['cpuPlayer']);

            if (array_key_exists($hash, $this->qTable)) {
                $actions = $this->qTable[$hash];
            } else {
                $actions = [0, 0, 0, 0, 0, 0, 0, 0, 0];
            }

            $actions[$action] += $item['reward'];
            $this->qTable[$hash] = $actions;
        }

        $this->states = [];
    }

    public function resetStates()
    {
        $this->states = [];
    }

    public function analyzePosition(array $state, array $coordinates, string $cpuPlayer)
    {
        $tmp = [];

        foreach ($state as $y => $row) {
            foreach ($row as $x => $cell) {
                $tmp[$y][$x] = trim($cell, '-');
            }
        }

        $state = $tmp;

        // Getting reward current position
        $reward = $this->getReward($state, $coordinates, $cpuPlayer);

        // Getting rating for next available positions
        $newState = $state;
        $newState[$coordinates[0]][$coordinates[1]] = $cpuPlayer;
        $freeCoordinates = $this->findFreeCoordinates($newState);
        $nextActions = [];
        $oponentCoordinates = $freeCoordinates;

        foreach ($freeCoordinates as $cpuCoords) {

            foreach ($oponentCoordinates as $oponentCoords) {
                $tmpState = $newState;

                if ($cpuCoords === $oponentCoords) {
                    continue;
                } else {
                    $tmpState[$oponentCoords[0]][$oponentCoords[1]] = $cpuPlayer == 'X' ? 'O' : 'X';
                    $nextActions[] = $this->getRatingFromQTable($tmpState, $cpuCoords, $cpuPlayer);
                }
            }

        }

        // Getting max rating
        $maxRate = 0;
        foreach ($nextActions as $rate) {
            if ($rate > $maxRate) {
                $maxRate = $rate;
            }
        }

        /* Q-Learn algorithm:
        *
        *    Q(state, action) = R(state, action) + Gamma * Max[Q(next state, all actions)]
        */
        $qRating = $reward + ($this->gamma * $maxRate);
        $this->addState($state, $coordinates, $qRating, $cpuPlayer);
    }

    public function findFreeCoordinates(array $state)
    {
        $freeCoordinates = [];

        foreach ($state as $y => $rows) {
            foreach ($rows as $x => $cell) {
                if (empty($cell)) {
                    $freeCoordinates[] = [$y, $x];
                }
            }
        }

        return $freeCoordinates;
    }

    private function convertCoordinatesToAction(array $coordinates)
    {
        // Convert coordinates to square number (from 0 to 8)
        return (($coordinates[0]*3)+$coordinates[1]+1) - 1;
    }

    public function ratePosition($boardState, $cpuPlayer)
    {
        $lines = [];
        $lines[] = [$boardState[0][0], $boardState[0][1], $boardState[0][2]];
        $lines[] = [$boardState[1][0], $boardState[1][1], $boardState[1][2]];
        $lines[] = [$boardState[2][0], $boardState[2][1], $boardState[2][2]];
        $lines[] = [$boardState[0][0], $boardState[1][0], $boardState[2][0]];
        $lines[] = [$boardState[0][1], $boardState[1][1], $boardState[2][1]];
        $lines[] = [$boardState[0][2], $boardState[1][2], $boardState[2][2]];
        $lines[] = [$boardState[0][0], $boardState[1][1], $boardState[2][2]];
        $lines[] = [$boardState[0][2], $boardState[1][1], $boardState[2][0]];

        $coordKeys = [];
        $coordKeys[] = ['00', '01', '02'];
        $coordKeys[] = ['10', '11', '12'];
        $coordKeys[] = ['20', '21', '22'];
        $coordKeys[] = ['00', '10', '20'];
        $coordKeys[] = ['01', '11', '21'];
        $coordKeys[] = ['02', '12', '22'];
        $coordKeys[] = ['00', '11', '22'];
        $coordKeys[] = ['02', '11', '20'];

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

            if ($cpuPlayer == 'X' && $playerX == 3) {
                return 1;
            }

            if ($cpuPlayer == 'O' && $playerO == 3) {
                return 1;
            }
        }

        return 0;
    }

}
