<?php

namespace app\middleware;

use app\models\User;
use pew\lib\Session;
use pew\request\Middleware;
use pew\request\Request;

class OnlyAuthenticated extends Middleware
{
    public function before(Session $session, Request $request)
    {
        if ($session->has(USER_KEY)) {
            return;
        }

        if ($request->cookies->has(SESSION_KEY)) {
            $user = User::loginWithToken($request->cookies->get(SESSION_KEY));

            if ($user) {
                return;
            }
        }

        $session->set("return_to", here());

        return $this->redirect("/login");
    }
}
