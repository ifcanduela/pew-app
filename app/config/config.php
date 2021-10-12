<?php

return [
    "app_title" => "Pew-Pew-Pew",

    "debug" => true,
    "show_debugbar" => false,
    "env" => "dev",

    "debugbar" => function ($c) {
        # The debug bar requires installing the maximebf/debugbar package
        $showDebugbar = $c["show_debugbar"];
        $debugbar = new \app\services\debugbar\DebugBar($showDebugbar);

        return $debugbar;
    },
];
