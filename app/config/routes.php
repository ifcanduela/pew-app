<?php

return [
    # 
    # login and logout
    # 

    '/login' => 'Users@login',
    '/logout' => 'Users@logout',

    #
    # general routes
    # 

    [
        'path' => '/welcome[/{name}]',
        'controller' => 'Welcome@index',
        'methods' => 'GET POST',
        'defaults' => [
            'name' => 'Pew'
        ],
    ],

    [
        'path' => '/',
        'controller' => 'Welcome@index',
        'defaults' => [
            'name' => 'Pew'
        ],
    ],
];
