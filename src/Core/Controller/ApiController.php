<?php

namespace Jarenal\Core\Controller;


class ApiController extends BaseController
{
    public function move()
    {
        $IA = $this->container->get('IA');
        $humanPlayer = $_POST['player'];
        $boardState = $_POST['boardState'];
        $game = $this->container->get('game');
        $coords = $game->makeMove($boardState, $humanPlayer, true, false);
        $cpuPlayer = $humanPlayer == 'X' ? 'O' : 'X';
        return json_encode(array_merge($coords, [$cpuPlayer]));
    }
}
