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
 *
 * Add $ to the end of the URL to make it strictly ignore any 
 * additional segments. Otherwise, those segments will be added to the
 * end of the result.
 */
return [

    # this last route will use the default controller and action
    ['/', '/pages/index'],

];
