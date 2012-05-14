<?php

# Add the PEAR directory to the global includes list for access to PHPUnit
# Change the path here to point to the proper PEAR path
# PHPUnit is usually installed to %PEAR_PATH%/PHPUnit
if (false === strpos('PEAR', get_include_path())) {
    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . __DIR__ . '/../../../php/PEAR');
}

# Simplify access to the framework files
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . __DIR__ . '/../sys/');

# This control constant may be discontinued in the future
define('PEWPEWPEW', microtime());

# Just to have a reference to the tests directory
define('TESTS_PATH', __DIR__);
