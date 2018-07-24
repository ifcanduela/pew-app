<?php

namespace app\controllers;

class Welcome extends \pew\Controller
{
    public function index(string $name)
    {
        $this->view->title("Welcome!");

        return ["name" => $name];
    }
}
