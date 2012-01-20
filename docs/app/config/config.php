<?php

/**
 * If DEBUG is 0, framework debugging messages will not be displayed.
 *
 * If DEBUG is 2 debug messages will be displayed even if the request is made
 * from a remote IP. Setting DEBUG to 1 is safer for testing and
 * production environments.
 */
define('DEBUG', 2);

/**
 * Set this to false (or any falsy value) to disable database Model
 * initialization, or to a string referring to the database config in
 * app/config/database_configuration.php.
 *
 * If USEDB is boolean true, the 'default' config will be used.
 */
define('USEDB', false);

/**
 * This indicates where the database connection details are stored.
 *
 * For added security, place database_configuration.php outside your document
 * root and even change its name.
 *
 * If this constant is not defined, database_configuration must be in the same
 * folder this file is.
 */
//define('DATABASE_CONFIGURATION', __DIR__ . DIRECTORY_SEPARATOR . 'database_configuration.php');

/**
 * Set this to true to enable user Authentication.
 */
define('USEAUTH', false);

/**
 * Set this to false to disable Sessions.
 */
define('USESESSION', false);

/*
 * The default title for your application.
 */
define('APPLICATION_TITLE', 'Pew-Pew-Pew Documentation');

/**
 * The Default controller.
 */
define('DEFAULT_CONTROLLER', 'book');

/**
 * The default action for the default controller.
 *
 * Default action for additional controllers is always 'index'.
 */
define('DEFAULT_ACTION', 'index');

/**
 * And the default layout.
 * 
 * The default layout file is in /sys/default/views/default.layout.php
 */
define('DEFAULT_LAYOUT', 'docs');

/**
 * The assets folder must be accesible via URL
 */
//define('WWW', URL . 'www' . PS);
