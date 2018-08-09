<?php

namespace app\middleware;

use pew\lib\Session;
use pew\request\Middleware;

class OnlyAuthenticated extends Middleware
{
    public function before(Session $session)
    {
        if (!$session->has(USER_KEY)) {
            $session->set("return_to", here());

            return $this->redirect("/login");
        }
    }
}
