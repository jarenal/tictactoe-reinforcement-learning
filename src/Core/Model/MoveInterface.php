<?php

namespace Jarenal\Core\Model;


interface MoveInterface
{
    public function makeMove($boardState, $playerUnit);
}