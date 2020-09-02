<?php

use pew\router\RouteBuilder as R;

use app\middleware\RedirectToPrevious;
use app\middleware\OnlyAuthenticated;
use app\middleware\ReturnJson;

R::get("/test[/{action}[/{id}]]")->to("test");

R::group(function () {
    R::from("[/{action}]")->to("api");
})
->prefix("/api")
->before([ReturnJson::class]);

#
# login and logout
#

R::from("/login")->to("users@login");
R::from("/logout")->to("users@logout");
R::from("/signup")->to("users@signup");

#
# protected routes
#

R::group(function () {
    R::from("[/{action}[/{id}]]")->to("admin")
        ->default("action", "index")
        ->default("id", null)
        ->name("admin");
})
->prefix("/admin")
->before([
    RedirectToPrevious::class,
    OnlyAuthenticated::class,
]);

#
# general routes
#

R::from("/welcome[/{name}]")
    ->handler("welcome@index")
    ->methods("get", "post")
    ->defaults(["name" => "Pew"]);

R::get("/")->to("welcome@index")->default("name", "Pew");
