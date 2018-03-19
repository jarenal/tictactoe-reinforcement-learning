<?php

use PHPUnit\Framework\TestCase;
use Jarenal\Core\Container;
use Jarenal\Core\Model\Game;

class GameTest extends TestCase
{
    public function testMakeMoveWithoutIAFreeSpace()
    {
        $game = Game::getInstance();
        $boardState = [['X', 'O', '-'], ['O', 'X', 'X'], ['O', 'X', 'O']];
        $playerUnit = 'X';
        $useIA = false;
        $training = false;
        $result = $game->makeMove($boardState, $playerUnit, $useIA, $training);
        $this->assertCount(2, $result);
        $this->assertEquals([2, 0], $result);
    }

    public function testMakeMoveWithoutIANoSpace()
    {
        $game = Game::getInstance();
        $boardState = [['X', 'O', 'X'], ['O', 'X', 'X'], ['O', 'X', 'O']];
        $playerUnit = 'X';
        $useIA = false;
        $training = false;
        $result = $game->makeMove($boardState, $playerUnit, $useIA, $training);
        $this->assertCount(0, $result);
        $this->assertEquals([], $result);
    }

    public function testMakeMoveWithIA()
    {
        $container = Container::getInstance();

        $boardState = [['X', '-', '-'], ['O', 'X', 'X'], ['O', 'X', 'O']];
        $boardStateCleaned = [['X', '', ''], ['O', 'X', 'X'], ['O', 'X', 'O']];
        $playerUnit = 'X';
        $cpuPlayer = 'O';
        $useIA = true;
        $training = false;

        $IA = $this->getMockBuilder(IA::class)
            ->setMethods(['findFreeCoordinates', 'getRatingFromQTable'])
            ->getMock();

        $IA->expects($this->once())
            ->method('findFreeCoordinates')
            ->with($boardStateCleaned)
            ->willReturn([[0, 1], [0, 2]]);

        $IA->expects($this->exactly(2))
            ->method('getRatingFromQTable')
            ->withConsecutive([$boardStateCleaned, [0, 1], $cpuPlayer], [$boardStateCleaned, [0, 2], $cpuPlayer])
            ->will($this->onConsecutiveCalls(3, 2));

        $container->set('IA', $IA);

        $game = Game::getInstance();

        $result = $game->makeMove($boardState, $playerUnit, $useIA, $training);
        $this->assertCount(2, $result);
        $this->assertEquals([1, 0], $result);
    }

    public function testFindWinnerPlayerX()
    {
        $boardState = [['X', '-', '-'], ['O', 'X', 'X'], ['O', 'X', 'X']];
        $game = Game::getInstance();
        $this->assertEquals('X', $game->findWinner($boardState));
    }

    public function testFindWinnerPlayerO()
    {
        $boardState = [['O', '-', '-'], ['O', 'X', 'X'], ['O', 'X', 'X']];
        $game = Game::getInstance();
        $this->assertEquals('O', $game->findWinner($boardState));
    }

    public function testFindWinnerPlayerT()
    {
        $boardState = [['X', 'O', 'O'], ['O', 'X', 'X'], ['O', 'X', 'O']];
        $game = Game::getInstance();
        $this->assertEquals('T', $game->findWinner($boardState));
    }
}