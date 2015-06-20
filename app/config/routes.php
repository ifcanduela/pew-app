<?php

/*
 * Add routes to this array, they will be checked from top to bottom.
 *
 * Use the Route::create() static method to create routes.
 *
 * The first argument specifies a URI to be matched. Placeholders can be
 * specified by surrounding a placeholder name with curly braces. If the name
 * if followed by a colon and a regular expression, the regex will be used to match the
 * segment. The default regex is [^/]+. You can append '/*' to the URI to allow
 * arbitrary extra segments.
 *
 * The second argument is a controller/action pair. Use the controller slug and the action
 * method name, separated by a forward slash.
 *
 * You can set paaceholders as optional by calling ->with($placeholder, $default) after
 * the ::create() method, but do not use this for placeholders in the middle of the URI.
 *
 * HTTP methods can be limited by calling the ->via($methods) with a string or array arguments
 * that contain HTTP verbs, like ->via('get post'), ->via('get', 'post')
 * or ->via(['get', 'post']).
 */

use pew\router\Route;

return [

    # this route redirects anything beginning with /welcome to /welcome/index
    Route::create('/welcome/{name}', 'welcome/index')->with('name', 'Dude'),

    # this last route will use the default controller and action
    Route::create('/', 'pages/index'),

];
