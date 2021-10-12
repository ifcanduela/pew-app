<?php

namespace app\controllers;

class Welcome extends \pew\Controller
{
    public function index(string $name)
    {
        $this->view->title("Welcome!");

        return $this
            ->render("welcome/index", ["name" => $name])
            ->header("X-Controller-Action", __METHOD__);
    }
}
