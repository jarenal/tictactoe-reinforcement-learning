<?php

use PHPUnit\Framework\TestCase;
use DI\ContainerBuilder;

class HomeControllerTest extends TestCase
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

    public function testIndex()
    {
        $IA = $this->getMockBuilder(\Jarenal\IA\IA::class)
            ->setConstructorArgs(['foo.txt', 0.8])
            ->setMethods(['save'])
            ->getMock();

        $this->container->set(\Jarenal\IA\IA::class, $IA);

        $view = $this->getMockBuilder(\Jarenal\View\View::class)
            ->setMethods(['render'])
            ->getMock();

        $view->expects($this->once())
            ->method('render')
            ->with('index.tpl', ["title" => "MyTicTacToe Game!!"])
            ->willReturn('abc');

        $this->container->set(\Jarenal\View\View::class, $view);

        $controller = $this->container->get(\Jarenal\Controller\HomeController::class);

        $this->assertSame('abc', $controller->index());
    }
}