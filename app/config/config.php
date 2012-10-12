<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package app
 */

/**
 * If DEBUG is greater than 0, debugging messages will be displayed.
 */
define('DEBUG', 1);

/**
 * Set this to false (or any falsy value) to disable database Model
 * initialization, or to a string referring to the database config in
 * app/config/database_configuration.php.
 *
 * For backwards-compatibility, if USEDB is boolean true, the 'default' config
 * will be used.
 */
define('USEDB', 'default');

/**
 * Set this to true to enable user Authentication.
 */
define('USEAUTH', false);

/**
 * Set this to false to disable Sessions.
 */
define('USESESSION', true);

/*
 * The default title for your application.
 */
define('APPLICATION_TITLE', 'Pew-Pew-Pew');

/**
 * The default controller.
 */
define('DEFAULT_CONTROLLER', 'pages');

/**
 * The default action.
 */
define('DEFAULT_ACTION', 'index');

/**
 * And the default layout.
 */
define('DEFAULT_LAYOUT', 'default');

/**
 * Configure global action prefixes. Default is ''
 */
//define('ACTION_PREFIX', 'action_');

/**
 * Additional configuration for app file extensions.
 **/
//define('MODEL_EXT', '.class.php');
//define('CONTROLLER_EXT', '.class.php');
//define('VIEW_EXT', '.php');
//define('ELEMENT_EXT', '.php');
//define('LAYOUT_EXT', '.layout.php');
//define('LIBRARY_EXT', '.class.php');