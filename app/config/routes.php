<?php

/*
 * Add routes to this array, they will be checked from top to bottom.
 *
 * A route consists of a possible URI (beginning with a forward slash) 
 * and a route (/controller/action[/parameters]) to transform the URL 
 * into.
 *
 * Use tags like :tag to match segments from the URI (left) to the 
 * route (right).
 *
 * A third array element restricts the HTTP methods for a given route. You 
 * can use any non-alphanumeric character to separate HTTP methods. If no 
 * methods are specified, GET is used.
 */
return [

    # this route redirects anything beginning with /welcome to /welcome/index
    ['/welcome/*', '/welcome/index/*'],

    # this last route will use the default controller and action
    ['/', '/pages/index'],

];
