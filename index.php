<?php

/**
 * Everything is relative.
 *
 * To this file.
 */

# framework bootstrap
require __DIR__ . '/pew/Pew.php';

# ...and the magic begins!
\pew\Pew::instance()->app('app', 'config')->run();
