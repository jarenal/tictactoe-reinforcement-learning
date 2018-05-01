<?php

use PHPUnit\Framework\TestCase;
use DI\ContainerBuilder;

class GameTest extends TestCase
{
    protected $container;

    public function setUp()
    {
        $builder = new ContainerBuilder();
        $builder->useAnnotations(true);
        $builder->addDefinitions([
            Jarenal\IA\IA::class => DI\factory(function () {
                $ia = new \Jarenal\IA\IA("foo.txt", 0.8);
                return $ia;
            })
        ]);
        $this->container = $builder->build();
        parent::setUp();
    }

    public function testMakeMoveWithoutIAFreeSpace()
    {
        $IA = $this->getMockBuilder(\Jarenal\IA\IA::class)
            ->setConstructorArgs(['foo.txt', 0.8])
            ->setMethods(['save'])
            ->getMock();

        $this->container->set(\Jarenal\IA\IA::class, $IA);

        $game = $this->container->get(\Jarenal\Model\Game::class);
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
        $IA = $this->getMockBuilder(\Jarenal\IA\IA::class)
            ->setConstructorArgs(['foo.txt', 0.8])
            ->setMethods(['save'])
            ->getMock();

        $this->container->set(\Jarenal\IA\IA::class, $IA);

        $game = $this->container->get(\Jarenal\Model\Game::class);
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
        $boardState = [['X', '-', '-'], ['O', 'X', 'X'], ['O', 'X', 'O']];
        $boardStateCleaned = [['X', '', ''], ['O', 'X', 'X'], ['O', 'X', 'O']];
        $playerUnit = 'X';
        $cpuPlayer = 'O';
        $useIA = true;
        $training = false;

        $IA = $this->getMockBuilder(\Jarenal\IA\IA::class)
            ->setConstructorArgs(['foo.txt', 0.8])
            ->setMethods(['findFreeCoordinates', 'getRatingFromQTable', 'save'])
            ->getMock();

        $IA->expects($this->once())
            ->method('findFreeCoordinates')
            ->with($boardStateCleaned)
            ->willReturn([[0, 1], [0, 2]]);

        $IA->expects($this->exactly(2))
            ->method('getRatingFromQTable')
            ->withConsecutive([$boardStateCleaned, [0, 1], $cpuPlayer], [$boardStateCleaned, [0, 2], $cpuPlayer])
            ->will($this->onConsecutiveCalls(3, 2));

        $this->container->set(\Jarenal\IA\IA::class, $IA);

        $game = $this->container->get(\Jarenal\Model\Game::class);

        $result = $game->makeMove($boardState, $playerUnit, $useIA, $training);
        $this->assertCount(2, $result);
        $this->assertEquals([1, 0], $result);
    }

    public function testFindWinnerPlayerX()
    {
        $IA = $this->getMockBuilder(\Jarenal\IA\IA::class)
            ->setConstructorArgs(['foo.txt', 0.8])
            ->setMethods(['save'])
            ->getMock();

        $this->container->set(\Jarenal\IA\IA::class, $IA);

        $boardState = [['X', '-', '-'], ['O', 'X', 'X'], ['O', 'X', 'X']];
        $game = $this->container->get(\Jarenal\Model\Game::class);
        $this->assertEquals('X', $game->findWinner($boardState));
    }

    public function testFindWinnerPlayerO()
    {
        $IA = $this->getMockBuilder(\Jarenal\IA\IA::class)
            ->setConstructorArgs(['foo.txt', 0.8])
            ->setMethods(['save'])
            ->getMock();

        $this->container->set(\Jarenal\IA\IA::class, $IA);

        $boardState = [['O', '-', '-'], ['O', 'X', 'X'], ['O', 'X', 'X']];
        $game = $this->container->get(\Jarenal\Model\Game::class);
        $this->assertEquals('O', $game->findWinner($boardState));
    }

    public function testFindWinnerPlayerT()
    {
        $IA = $this->getMockBuilder(\Jarenal\IA\IA::class)
            ->setConstructorArgs(['foo.txt', 0.8])
            ->setMethods(['save'])
            ->getMock();

        $this->container->set(\Jarenal\IA\IA::class, $IA);

        $boardState = [['X', 'O', 'O'], ['O', 'X', 'X'], ['O', 'X', 'O']];
        $game = $this->container->get(\Jarenal\Model\Game::class);
        $this->assertEquals('T', $game->findWinner($boardState));
    }
}