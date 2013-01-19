<?php

/*
 * Add routes to this array,, they will be processed from top to bottom.
 *
 * A route consist of a possible URL (beginning with a forward slash) and 
 * a controller/action/parameters string to transform the URL into.
 *
 * You can add $ to the end of the URL to make it strictly ignore any 
 * addiotional segments. Otherwise, those segments will be added to the
 * end of the result.
 */
return array(
    '/*' => 'pages/view/:1',
    '/' => 'pages/index',
);
