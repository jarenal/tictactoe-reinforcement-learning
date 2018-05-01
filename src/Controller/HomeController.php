<?php

namespace Jarenal\Controller;


class HomeController extends BaseController
{
    public function index()
    {
        return $this->view->render("index.tpl", ["title" => "MyTicTacToe Game!!"]);
    }
}
