<?php

use pew\lib\Session;
use app\models\User;

return [
    "app_title" => "Pew-Pew-Pew",

    "debug" => true,

    "env" => "dev",

    # closures will receive the injection container as first argument
    "user" => function ($c) {
        /** @var Session */
        $session = $c["session"];

        if ($user_id = $session->get("user_id")) {
            return User::findOne($user_id);
        }

        return null;
    },
];
