<?php

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

$cfg = array();

/**
 * Server string. This goes before URL to assemble a full server URL.
 *
 * @var string
 */
$cfg['host'] = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'];

/**
 * Root path (url), '/' in case of root installation.
 *
 * @var string
 */
$cfg['path'] = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

/**
 * Full path (url) to the app, including server name and folder path.
 *
 * @var string
*/
$cfg['app_url'] = $cfg['host'] . $cfg['path'];

/**
 * Full path to the base folder (filesystem).
 *
 * @var string
 */
$cfg['root_folder'] = getcwd() . DS;

/**
 * Full path to the framework folder (filesystem).
 *
 * @var string
 * @name SYSTEM
 */
$cfg['system_folder'] = dirname(__FILE__) . DS;

/**
 * Framework version numbers.
 */
$cfg['version_major'] = '0';
$cfg['version_minor'] = '83';
$cfg['version_date'] = '2012-12-06';

/**
 * Whether the App is running on the localhost space.
 */
$cfg['localhost'] = in_array($_SERVER['REMOTE_ADDR'], array('localhost', '127.0.0.1', '::1'));

/**
 * Option to use a prefix for action method names in controllers.
 */
$cfg['action_prefix'] = '';

/**
 * Define DEBUG if it's not defined in app/config 
 */
$cfg['debug'] = false;

/**
 * Define DEBUG if it's not defined in app/config 
 */
$cfg['log_level'] = 0;


/**
 * Define extensions if user didn't.
 *
 * The values must include the first period (i.e., '.php')
 */
$cfg['model_ext'] = '.class.php';
$cfg['controller_ext'] = '.class.php';
$cfg['view_ext'] = '.php';
$cfg['element_ext'] = '.php';
$cfg['layout_ext'] = '.layout.php';
$cfg['library_ext'] = '.class.php';

/**
 * Set to true to use the Twig templating library for views.
 */
$cfg['use_twig'] = false;

require __DIR__ . DS .'functions.php';
require __DIR__ . DS .'pew.class.php';

Pew::config($cfg);

/**
 * Application configuration
 */
if (file_exists(APP . 'config' . DS . 'config.php')) {
    require_once APP . 'config' . DS . 'config.php';
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
 * Enable strict error reporting according to config.
 */
if ((DEBUG && PEW_LOCAL) || DEBUG > 1) {
	error_reporting(E_ALL | E_STRICT);
}

/**
 * Define main component constants if user didn't.
 */
defined('DEFAULT_CONTROLLER') or define('DEFAULT_CONTROLLER', 'pages');
defined('DEFAULT_ACTION')     or define('DEFAULT_ACTION', 'index');
defined('DEFAULT_LAYOUT')     or define('DEFAULT_LAYOUT', '');

/**
 * This one is somewhat hidden from the user.
 */
defined('DEFAULT_RESPONSE_FORMAT') or define('DEFAULT_RESPONSE_FORMAT', RESPONSE_FORMAT_HTML);

/**
 * Include the autoloading functions
 */
require_once SYSTEM . 'pew_loaders.php';

/**
 * Include basic functions
 */
require_once SYSTEM . 'functions.php';

/**
 * Application bootstrapping
 */
if (file_exists(APP . 'config' . DS . 'bootstrap.php')) {
	include APP . 'config' . DS . 'bootstrap.php';
}

