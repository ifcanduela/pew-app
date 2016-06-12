<?php

return [
    'use_db' => 'sqlite',

    'sqlite' => [
        'engine' => 'sqlite',
        'file' => 'app/config/db.sqlite3'
    ],

    'mysql' => [
        'engine' => 'mysql',
        'host' => 'localhost',
        'user' => 'username',
        'pass' => 'password',
        'name' => 'database name'
    ],
];
