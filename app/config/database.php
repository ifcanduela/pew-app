<?php

return [
    # if this key is not set, the database configuration will be the same as
    # `env` in the main config file, or `dev` if no `env` is set
    // "use_db" => "dev",

    "log_queries" => false,

    "dev" => [
        "engine" => "sqlite",
        "file" => root("data", "dev.db"),
    ],

    "prod" => [
        "engine" => "mysql",
        "host" => "localhost",
        "user" => "username",
        "pass" => "password",
        "name" => "database name"
    ],
];
