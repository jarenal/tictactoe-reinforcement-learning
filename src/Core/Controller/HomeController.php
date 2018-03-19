<?php

namespace Jarenal\Core\Controller;


class HomeController extends BaseController
{
    public function index()
    {
        return $this->container->get('view')->render("index.tpl", ["title" => "MyTicTacToe Game!!"]);
    }
}
