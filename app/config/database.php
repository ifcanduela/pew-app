<?php

return [

	'default' => 'sqlite',

    'sqlite' => [
        'engine' => SQLITE,
        'file' => 'app/config/db.sqlite3'
    ],

    'mysql' => [
        'engine' => MYSQL,
        'host' => 'localhost',
        'user' => 'username',
        'pass' => 'password',
        'name' => 'database name'
    ],

];
