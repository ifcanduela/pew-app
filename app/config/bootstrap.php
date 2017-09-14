<?php

const SESSION_KEY = 'MjgzOTkyNzY3MjIyODc2';

function app_title(...$page_title)
{
    $page_title[] = pew('app_title');

    return join(' | ', $page_title);
}
