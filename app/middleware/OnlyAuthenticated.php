<?php

namespace app\middleware;

use app\models\User;
use pew\lib\Session;
use pew\request\Middleware;

class OnlyAuthenticated extends Middleware
{
    public function before(Session $session, ?User $user)
    {
        if (!$user) {
            $session->set("return_to", here());

            return $this->redirect("/login");
        }
    }
}
