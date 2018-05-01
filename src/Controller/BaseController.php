<?php

namespace Jarenal\Controller;

use DI\Annotation\Inject;

class BaseController
{
    /**
     * @Inject("Jarenal\Model\Game")
     */
    protected $game;

    /**
     * @Inject("Jarenal\View\View")
     */
    protected $view;
}