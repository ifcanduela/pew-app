<?php

require_once __DIR__ . "/vendor/autoload.php";

$app = new \pew\console\App("app", "config");
$tm = $app->get("tableManager");
$dev = $tm->getConnection("dev");

return [
    "paths" => [
        "migrations" => "data/migrations",
        "seeds" => "data/seeds",
    ],

    "environments" => [
        "default_database" => "development",

        "development" => [
            "name" => "should_not_matter",
            "connection" => $dev,
        ],

        "production" => [
            "adapter" => "mysql",
            "host" => "localhost",
            "name" => "production_db",
            "user" => "root",
            "pass" => "",
            "port" => "3306",
            "charset" => "utf8",
        ],
    ],
];
