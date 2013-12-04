<?php

# Assorted functions
require_once __DIR__ . '/functions.php';

# Autoloader class definition
require_once __DIR__ . '/Autoloader.php';

# Autoload framework classes
(new \pew\Autoloader('pew', dirname(__DIR__)))->register();

# Configure the Pew object
\pew\Pew::instance();
