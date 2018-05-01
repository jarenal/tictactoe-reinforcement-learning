<?php

use PHPUnit\Framework\TestCase;
use DI\ContainerBuilder;

class ApiControllerTest extends TestCase
{
    protected $container;

    public function setUp()
    {
        $builder = new ContainerBuilder();
        $builder->useAnnotations(true);
        $this->container = $builder->build();
        parent::setUp();
    }

    public function testMove()
    {
        $_POST['player'] = 'X';
        $_POST['boardState'] = ['a', 'b', 'c'];

        $game = $this->getMockBuilder(Game::class)
            ->setMethods(['makeMove'])
            ->getMock();

        $game->expects($this->once())
            ->method('makeMove')
            ->with(['a', 'b', 'c'], 'X', true, false)
            ->willReturn([1, 1]);

        $this->container->set(\Jarenal\Model\Game::class, $game);

        $controller = $this->container->get(\Jarenal\Controller\ApiController::class);

        $this->assertSame(json_encode([1, 1, 'O']), $controller->move());
    }
}