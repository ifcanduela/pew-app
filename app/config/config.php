<?php

use pew\lib\Session;
use app\models\User;

return [
    "app_title" => "Pew-Pew-Pew",

    "env" => "dev",

    # closures will receive the injection container as argument
    "user" => function ($c) {
        /** @var Session */
        $session = $c["session"];

        /** @var User|null */
        $user = null;

        if ($user_id = $session->get("user_id")) {
            return User::findOne($user_id);
        }

        return $user;
    },

    "debug" => true,
];
