<?php

namespace Jarenal\Controller;


class ApiController extends BaseController
{
    public function move()
    {
        $humanPlayer = $_POST['player'];
        $boardState = $_POST['boardState'];
        $coords = $this->game->makeMove($boardState, $humanPlayer, true, false);
        $cpuPlayer = $humanPlayer == 'X' ? 'O' : 'X';
        return json_encode(array_merge($coords, [$cpuPlayer]));
    }
}
