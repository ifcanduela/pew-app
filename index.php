<?php

/**
 * Everything is relative.
 *
 * To this file.
 * 
 * @name Front controller
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package app
 */

# framework bootstrap
require 'sys/pew.class.php';

# ...and the magic begins!
try {
    Pew::app('app')->run('url');
} catch (Exception $e) {
    echo $e->getMessage();
}
