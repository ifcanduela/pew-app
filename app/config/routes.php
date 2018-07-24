<?php

use pew\router\Route;
use app\middleware\RedirectToPrevious;
use app\middleware\OnlyAuthenticated;

return [
    #
    # examples
    #

    // [
    //     "path" => "/post[/{slug}]",
    //     "controller" => "Posts@view",
    //     "methods" => "GET",
    //     "defaults" => [
    //         "slug" => "home"
    //     ]
    // ],

    // Route::from("/post[/{slug}]")
    //     ->handler("Posts@view")
    //     ->methods("get")
    //     ->defaults(["slug" => "home"]),

    // "/post[/{slug}]" => "Posts@view",

    #
    # login and logout
    #

    "/login" => "UsersController@login",
    "/logout" => "UsersController@logout",
    "/signup" => "UsersController@signup",

    #
    # protected routes
    #
    
    Route::group()->prefix("/admin")->routes([
        Route::from("[/{action}[/{id}]]")->to("AdminController")
            ->default("action", "index")
            ->default("id", null),
    ])->before([
        RedirectToPrevious::class,
        OnlyAuthenticated::class,
    ]),

    #
    # general routes
    #

    Route::from("/welcome[/{name}]")
        ->handler("Welcome@index")
        ->methods("get", "post")
        ->defaults(["name" => "Pew"]),

    [
        "path" => "/",
        "handler" => "Welcome@index",
        "defaults" => [
            "name" => "Pew"
        ],
    ],
];
