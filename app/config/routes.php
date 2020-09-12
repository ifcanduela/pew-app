<?php

use app\middleware\RedirectToPrevious;
use app\middleware\OnlyAuthenticated;
use app\middleware\ReturnJson;
use ifcanduela\router\Group;
use ifcanduela\router\Router;

/** @var Router $router */

$router->before(app\middleware\LoginWithToken::class);

$router->get("/test[/{action}[/{id}]]")->to("test");

$router->group("/api", function (Group $group) {
    $group->before(ReturnJson::class);
    $group->from("[/{action}]")->to("api");
});

#
# login and logout
#

$router->from("/login")->to("users@login");
$router->from("/logout")->to("users@logout");
$router->from("/signup")->to("users@signup");

#
# protected routes
#

$router->group("/admin", function (Group $group) {
    $group->before(RedirectToPrevious::class, OnlyAuthenticated::class);

    $group->from("[/{action}[/{id}]]")->to("admin")
        ->default("action", "index")
        ->default("id", null)
        ->name("admin");
});

#
# general routes
#

$router->from("/welcome[/{name}]")->to("welcome@index")
    ->methods("get", "post")
    ->defaults(["name" => "Pew"]);

$router->get("/")->to("welcome@index")->default("name", "Pew");
