<?php

namespace app\middleware;

use app\models\User;
use pew\App;
use pew\lib\Session;
use pew\request\Middleware;
use pew\request\Request;

class LoginUser extends Middleware
{
    public function before(Request $request, Session $session, App $app)
    {
        $user = null;

        if ($session->has(SESSION_KEY)) {
            $user = User::findOne($session->get(SESSION_KEY));
        } elseif ($request->cookies->has(COOKIE_KEY)) {
            $user = User::findOneByLoginToken($request->cookies->get(COOKIE_KEY));
            $session->set("user_id", $user->id);
        }

        $app->set(User::class, $user);
        $app->set("user", $user);
    }
}
