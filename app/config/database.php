<?php

return [
    # if this key is not set, the database configuration will be 'default'
    'use_db' => 'dev',

    'log_queries' => false,

    'dev' => [
        'engine' => 'sqlite',
        'file' => root('app/data/dev.sqlite'),
    ],

    'prod' => [
        'engine' => 'mysql',
        'host' => 'localhost',
        'user' => 'username',
        'pass' => 'password',
        'name' => 'database name'
    ],
];
