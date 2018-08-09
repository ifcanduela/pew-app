<?php

const SESSION_KEY = "MjgzOTkyNzY3MjIyODc2";
const USER_KEY = "user_id";

function app_title(...$page_title)
{
    $page_title[] = pew("app_title");

    return join(" | ", array_filter($page_title));
}
