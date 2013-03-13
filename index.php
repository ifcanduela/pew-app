<?php

/**
 * Everything is relative.
 *
 * To this file.
 */

# framework bootstrap
require __DIR__ . '/pew/Pew.php';

# ...and the magic begins!
\pew\Pew::app('app', 'config')->run();
