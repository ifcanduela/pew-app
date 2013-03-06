<?php

/**
 * Everything is relative.
 *
 * To this file.
 */

# framework bootstrap
require __DIR__ . '/pew/Pew.php';

# ...and the magic begins!
try {
	pew\Pew::app('app', 'config')->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
