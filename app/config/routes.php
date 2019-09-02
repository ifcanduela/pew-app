<?php

use pew\router\Route;
use app\middleware\RedirectToPrevious;
use app\middleware\OnlyAuthenticated;

return [
    #
    # login and logout
    #

    "/login" => "users@login",
    "/logout" => "users@logout",
    "/signup" => "users@signup",

    #
    # protected routes
    #

    Route::group()->prefix("/admin")->routes([
        Route::from("[/{action}[/{id}]]")->to("admin")
            ->default("action", "index")
            ->default("id", null)
            ->name("admin"),
    ])->before([
        RedirectToPrevious::class,
        OnlyAuthenticated::class,
    ]),

    #
    # general routes
    #

    Route::from("/welcome[/{name}]")
        ->handler("welcome@index")
        ->methods("get", "post")
        ->defaults(["name" => "Pew"]),

    Route::from("/")->to("welcome@index")->default("name", "Pew"),
];
