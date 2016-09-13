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

    Route::from('/welcome[/{name}]')
        ->handler('Welcome@index')
        ->methods('get post')
        ->defaults(['name' => 'Pew']),

    [
        'path' => '/',
        'handler' => 'Welcome@index',
        'defaults' => [
            'name' => 'Pew'
        ],
    ],
];
