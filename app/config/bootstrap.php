<?php

use ifcanduela\events\EventManager as E;

const SESSION_KEY = "MjgzOTkyNzY3MjIyODc2";
const USER_KEY = "user_id";

E::register("app.init", function () {
    # Initialize the debugbar
    if (pew("show_debugbar")) {
        $db = pew("debugbar");

        if (pew("request")->acceptsJson()) {
            $db->sendDataInHeaders();
        }
    }
});

function app_title(...$page_title)
{
    $page_title[] = pew("app_title");

    return join(" | ", array_filter($page_title));
}

function user()
{
    static $user;

    if ($user === null) {
        $session = pew("session");

        if (isset($session["user"])) {
            $userId = $session->get(USER_KEY);
            $user = \app\models\User::findOneById($userId);
        } else {
            $user = false;
        }
    }

    return $user;
}

function pew(...$arguments)
{
    return \pew\pew(...$arguments);
}

function flash(...$arguments)
{
    return \pew\flash(...$arguments);
}

function url(...$arguments): string
{
    return \pew\url(...$arguments);
}

function here(): string
{
    return \pew\here();
}

function root(...$arguments): string
{
    return \pew\root(...$arguments);
}

function route(...$arguments): \pew\lib\Url
{
    return \pew\route(...$arguments);
}


function is_route(...$arguments): bool
{
    return \pew\is_route(...$arguments);
}
