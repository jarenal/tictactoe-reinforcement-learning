<?php

use PHPUnit\Framework\TestCase;
use Jarenal\Core\Container;

class HomeControllerTest extends TestCase
{
    public function testIndex()
    {
        $container = Container::getInstance();

        $view = $this->getMockBuilder(\Jarenal\Core\View\View::class)
            ->setMethods(['render'])
            ->getMock();

        $view->expects($this->once())
            ->method('render')
            ->with('index.tpl', ["title" => "MyTicTacToe Game!!"])
            ->willReturn('abc');

        $container->set('view', $view);

        $controller = new \Jarenal\Core\Controller\HomeController($container);

        $this->assertSame('abc', $controller->index());
    }
}