<?php

/**
 * Everything is relative.
 *
 * To this file.
 */

# framework bootstrap
require __DIR__ . '/pew/bootstrap.php';

# ...and the magic begins!
$app = new \pew\App('app', 'config');
$app->run();
