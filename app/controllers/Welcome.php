<?php

namespace app\controllers;

class Welcome extends \pew\Controller
{
    public function index($name)
    {
        $this->view->title('Welcome!');

        return ['name' => $name];
    }
}
