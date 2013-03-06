<?php

return [

	'default' => 'sqlite',

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
