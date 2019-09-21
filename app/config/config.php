<?php

use pew\lib\Session;
use app\models\User;

return [
    "app_title" => "Pew-Pew-Pew",

    "debug" => true,
    "show_debugbar" => false,
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

    "debugbar" => function ($c) {
        # The debug bar requires installing the maximebf/debugbar package
        $showDebugbar = $c["show_debugbar"];
        $debugbar = new \app\services\debugbar\DebugBar($showDebugbar);

        return $debugbar;
    },
];
