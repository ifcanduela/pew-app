<?php

return [
    # if this key is not set, the database configuration will be 'default'
    'use_db' => 'dev',

    'dev' => [
        'engine' => 'sqlite',
        'file' => root('db/dev.sqlite'),
    ],

    'prod' => [
        'engine' => 'mysql',
        'host' => 'localhost',
        'user' => 'username',
        'pass' => 'password',
        'name' => 'database name'
    ],
];
