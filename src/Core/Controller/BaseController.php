<?php

namespace Jarenal\Core\Controller;

use Jarenal\Core\Container;

class BaseController
{
    protected $container;

    public function __construct($container = false)
    {
        if ($container) {
            $this->container = $container;
        } else {
            $this->container = Container::getInstance();
        }

    }
}