<?php

namespace app\middleware;

use pew\libs\Session;
use pew\request\Middleware;

class OnlyAuthenticated extends Middleware
{
    public function before(Session $session)
    {
        if (!$session->has("user")) {
            $session->set("return_to", here());

            return $this->redirect("/login");
        }
    }
}
