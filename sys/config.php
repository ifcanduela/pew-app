<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

/******************************************************************************
 *     P E W - P E W - P E W   D E F A U L T   C O N F I G U R A T I O N      *
 *******************************IT**BEGINS**NAO********************************/

/**
 * Filesystem separator.
 *
 * @var string
 * @name DS
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Url path separator, just because.
 *
 * @var string
 * @name PS
 */
define('PS', '/');

/**
 * Root path (url), '/' in case of root installation.
 *
 * @var string
 * @name URL
 */
define('URL', str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));

/**
 * Full path to the base folder (filesystem).
 *
 * @var string
 * @name ROOT
 */
defined ('ROOT') or define('ROOT', getcwd() . DS);

/**
 * Full path to the application folder (filesystem).
 *
 * @var string
 * @name APP
 */
defined('APP') or define('APP', ROOT  . 'app' . DS);

/**
 * Full path to the framework folder (filesystem).
 *
 * @var string
 * @name SYSTEM
 */
defined('SYSTEM') or define('SYSTEM', dirname(__FILE__) . DS);

/**
 * Framework source file folders (filesystem).
 */
define('CONTROLLERS',  APP    . 'controllers' . DS);
define('MODELS',       APP    . 'models'      . DS);
define('VIEWS',        APP    . 'views'       . DS);
define('ELEMENTS',     VIEWS  . 'elements'    . DS);
define('LIBRARIES',    APP    . 'libs'        . DS);
define('DEFAULT',      SYSTEM . 'default'     . DS);

/**
 * Error type constants. Not many errors.
 */
define('NO_ERROR', 0);
define('VIEW_MISSING', 1);
define('LAYOUT_MISSING', 2);
define('CONTROLLER_MISSING', 3);
define('ACTION_MISSING', 4);
define('ELEMENT_MISSING', 5);
define('CONTROLLER_FILE_MISSING', 6);
define('CONTROLLER_CLASS_MISSING', 7);
define('MODEL_FILE_MISSING', 8);
define('MODEL_CLASS_MISSING', 9);
define('LIBRARY_FILE_MISSING', 10);
define('LIBRARY_CLASS_MISSING', 11);
define('ACTION_FORBIDDEN', 12);

/**
 * Framework version numbers.
 */
define('MAJOR_VERSION', '0');
define('MINOR_VERSION', '82');
define('VERSION_DATE', '2011-11-29');
define('VERSION', MAJOR_VERSION . '.' . MINOR_VERSION . '.' . VERSION_DATE);

/**
 * Output formats for actions
 */
define('RESPONSE_FORMAT_HTML', 'html');
define('RESPONSE_FORMAT_XML', 'xml');
define('RESPONSE_FORMAT_JSON', 'json');

define('OUTPUT_TYPE_HTML', 'html');
define('OUTPUT_TYPE_XML', 'xml');
define('OUTPUT_TYPE_JSON', 'json');

/**
 * Whether the App is running on the localhost space.
 */
define('PEW_LOCAL', defined('STDIN') or 'localhost' == $_SERVER['REMOTE_ADDR'] || '127.0.0.1' == $_SERVER['REMOTE_ADDR']);

/**
 * Application configuration
 */
if (file_exists(APP . 'config' . DS . 'config.php')) {
    require APP . 'config' . DS . 'config.php';
}

/**
 * Full path to the assets folder (url).
 *
 * @var string
 * @name WWW
 */
defined('WWW') or define('WWW', URL . 'www' . PS);

/**
 * General components
 *
 */
defined('USESESSION') or define('USESESSION', false);
defined('USEDB')      or define('USEDB', false);
defined('USEAUTH')    or define('USEAUTH', false);

/**
 * Define DEBUG if it's not defined in app/config 
 */
defined ('DEBUG')     or define('DEBUG', 0);

/**
 * Enable strict error reporting according to config.
 */
if ((DEBUG && PEW_LOCAL) || DEBUG > 1) {
	error_reporting(E_ALL | E_STRICT);
}

/**
 * Define constants if user didn't.
 */
defined('DEFAULT_CONTROLLER') or define('DEFAULT_CONTROLLER', 'pages');
defined('DEFAULT_ACTION')     or define('DEFAULT_ACTION', 'index');
defined('DEFAULT_LAYOUT')     or define('DEFAULT_LAYOUT', '');

/**
 * This one is somewhat hidden from the user.
 */
defined('DEFAULT_RESPONSE_FORMAT') or define('DEFAULT_RESPONSE_FORMAT', RESPONSE_FORMAT_HTML);

/**
 * Define extensions if user didn't.
 *
 * The values must include the first period (i.e., '.php')
 */
defined('MODEL_EXT')      or define('MODEL_EXT', '.class.php');
defined('CONTROLLER_EXT') or define('CONTROLLER_EXT', '.class.php');
defined('VIEW_EXT')       or define('VIEW_EXT', '.php');
defined('ELEMENT_EXT')    or define('ELEMENT_EXT', '.php');
defined('LAYOUT_EXT')     or define('LAYOUT_EXT', '.layout.php');
defined('LIBRARY_EXT')    or define('LIBRARY_EXT', '.php');

defined('USETWIG')        or define('USETWIG', false);

/**
 * Include the autoloading functions
 */
require SYSTEM . 'pew_loaders.php';

/**
 * Include basic functions
 */
require SYSTEM . 'functions.php';

/**
 * Application bootstrapping
 */
if (file_exists(APP . 'config' . DS . 'bootstrap.php')) {
	include APP . 'config' . DS . 'bootstrap.php';
}

