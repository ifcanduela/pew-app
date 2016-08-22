<?php

use pew\router\Route;

return [
    #
    # examples
    #

    // [
    //     'path' => '/post[/{slug}]',
    //     'controller' => 'Posts@view',
    //     'methods' => 'GET',
    //     'defaults' => [
    //         'slug' => 'home'
    //     ]
    // ],

    // Route::from('/post[/{slug}]')
    //     ->handler('Posts@view')
    //     ->methods('get')
    //     ->defaults(['slug' => 'home']),

    // '/post[/{slug}]' => 'Posts@view',

    #
    # login and logout
    #

    '/login' => 'Users@login',
    '/logout' => 'Users@logout',
    '/signup' => 'Users@signup',

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
