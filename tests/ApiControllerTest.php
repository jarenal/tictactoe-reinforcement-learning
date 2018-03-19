<?php

use PHPUnit\Framework\TestCase;
use Jarenal\Core\Container;

class ApiControllerTest extends TestCase
{
    public function testMove()
    {
        $_POST['player'] = 'X';
        $_POST['boardState'] = ['a', 'b', 'c'];

        $container = Container::getInstance();

        $IA = $this->getMockBuilder(IA::class)
            ->setMethods(['analyzePosition'])
            ->getMock();

        $IA->expects($this->once())
            ->method('analyzePosition')
            ->with(['a', 'b', 'c'], [1, 1], 'O');

        $container->set('IA', $IA);

        $game = $this->getMockBuilder(Game::class)
            ->setMethods(['makeMove'])
            ->getMock();

        $game->expects($this->once())
            ->method('makeMove')
            ->with(['a', 'b', 'c'], 'X', true, false)
            ->willReturn([1, 1]);

        $container->set('game', $game);

        $controller = new \Jarenal\Core\Controller\ApiController($container);

        $this->assertSame(json_encode([1, 1, 'O']), $controller->move());
    }
}