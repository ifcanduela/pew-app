<?php

return [
    'dev' => [
        'engine' => 'sqlite',
        'file' => 'app/config/db.sqlite3'
    ],

    'prod' => [
        'engine' => 'mysql',
        'host' => 'localhost',
        'user' => 'username',
        'pass' => 'password',
        'name' => 'database name'
    ],
];
