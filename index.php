<?php

/**
 * Everything is relative.
 *
 * To this file.
 */

# framework bootstrap
require __DIR__ . '/pew/pew.class.php';

# ...and the magic begins!
try {
    Pew::app('app', 'config')->run('url');
} catch (Exception $e) {
    echo $e->getMessage();
}
