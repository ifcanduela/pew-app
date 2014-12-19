<?php

namespace app\controllers;

use pew\libs\Request;

class Base extends \pew\Controller
{
    public function before_action(Request $request)
    {
        
    }

    public function after_action(array $view_data)
    {
        return $view_data;
    }
}
