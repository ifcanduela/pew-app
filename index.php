<?php

/**
 * Everything is relative.
 *
 * To this file.
 * 
 * @name Front controller
 * @version 0.14 29-sep-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package app
 */

# protection against direct access to scripts
define('PEWPEWPEW', true);

# framework configuration
require 'sys/config.php';

# some benchmarking
cfg('start_time', get_execution_time());

try {
    # ...and the magic begins!
    Pew::Get('App')->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
