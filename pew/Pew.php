<?php

namespace pew;

# Assorted functions
require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';
# Autoloader class definition
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Autoloader.php';

use \pew\libs\Registry as Registry;

/**
 * An object store.
 * 
 * The Pew class is a hybrid registry/factory that contains singleton-like
 * instances of classes in the framework. It's implemented as a collection
 * of static methods that return instances of Controllers, Models and other 
 * classes.
 * 
 * @package pew
 * @author ifcanduela <ifcanduela@gmail.com>
 */
class Pew
{
    /**
     * Framework and application configuration settings.
     * 
     * @var \pew\libs\Registry
     */
    protected static $config = null;

    /**
     * Constructor is out of bounds.
     *
     * @throws Exception
     */
    protected function __construct()
    {

    }

    /**
     * Obtains an object of the specified class.
     * 
     * The $arguments parameter is used in the call to the class constructor.
     * 
     * @param string $index Index in the storage
     * @param mixed $arguments A single argument or an array of arguments
     * @return Object An instance of the required class
     */
    public static function get($index, $arguments = [])
    {
        $registry = Registry::instance();
        if (!isset($registry->$index)) {
            if (class_exists($index)) {
                $reflection_class = new \ReflectionClass($index);
                $registry->$index = $reflection_class->newInstanceArgs($arguments);
            } else {
                throw new \Exception("Class $index could not be found.");
            }
        }
        
        return $registry->$index;
    }

    /**
     * Obtains the current instance of the Pew-Pew-Pew application.
     *
     * The folder parameter must be a sub-folder of the folder in which the
     * main index.php file resides.
     *
     * @param $app_folder Folder name that holds the application folders and files
     * @return App Instance of the application
     */
    public static function app($app_folder = 'app', $config_file = 'config')
    {
        if (!isset($registry->App)) {
            $appLoader = new Autoloader($app_folder, dirname(realpath($app_folder)));
            $appLoader->register();
            
            $pewLoader = new Autoloader('pew', dirname(__DIR__));
            $pewLoader->register();
            
            $registry = Registry::instance();

            $pew_config = require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
            self::$config = new Registry();
            self::$config->import($pew_config);

            # load app/config/{$config}.php
            $app_config = include getcwd() . DS . $app_folder . DS . 'config' . DS . $config_file . '.php';

            // merge user config with Pew config
            self::$config->import($app_config);

            if (!self::$config->env) {
                self::$config->env = 'development';
            }

            # add application namespace and path
            $app_folder_name = trim(basename($app_folder));
            self::$config->app_namespace = '\\' . $app_folder_name;
            self::$config->app_folder = realpath($app_folder);
            self::$config->app_config = $config_file;

            # load app/config/bootstrap.php
            if (file_exists(self::$config->app_folder . '/config' . DS . 'bootstrap.php')) {
                require self::$config->app_folder . '/config' . DS . 'bootstrap.php';
            }

            $registry->App = new App($app_folder);
        }

        return $registry->App;
    }

    /**
     * Merges configuration arrays an return the resulting configuration.
     * 
     * @param array $config An array with configuration keys
     * @return Registry Object with configuration properties
     */
    public static function config(array $config = null)
    {
        if ($config) {
            self::$config->import($config);
        }

        return self::$config;
    }

    /**
     * Obtains a controller instance of the specified class.
     * 
     * @param string $controller_name Name of the controller class
     * @param Request $request a Request object
     * @return Object An instance of the required Controller
     * @throws InvalidArgumentException When no current controller exists and no class name is provided
     */
    public static function controller($controller_name = null, \pew\libs\Request $request)
    {   
        $registry = Registry::instance();

        # check if the class name is omitted
        if (!isset($controller_name)) {
            if (isset($registry->CurrentRequestController)) {
                # if exists, return the current controller
                return $registry->CurrentRequestController;
            } else {
                # if not, throw an exception
                throw new \InvalidArgumentException("No current controller could be retrieved");
            }
        } else {
            $class_name = file_name_to_class_name($controller_name);

            $app_class_name = self::$config->app_namespace . '\\controllers\\' . $class_name;
            $pew_class_name = '\\pew\\controllers\\' . $class_name;
            
            if (class_exists($app_class_name)) {
                return new $app_class_name($request);
            } elseif (class_exists($pew_class_name)) {
                return new $pew_class_name($request);
            }
        }

        return false;
    }

    /**
     * Obtains a model instance of the specified class.
     * 
     * This function returns a generic model if the specific model class is not
     * defined.
     *
     * @param string $table_name Name of the table
     * @return Object An instance of the required Model
     */
    public static function model($table_name)
    {
        $registry = Registry::instance();

        $class_name = self::$config->app_namespace . '\\models\\' . file_name_to_class_name($table_name) . 'Model';
        $class_base_name = class_base_name($class_name);

        # Check that the model has not been previously instantiated
        if (!isset($registry->$class_name)) {
            # Instantiate Model if the derived class is not available
            if (!class_exists($class_name)) {
                $class_name = '\\pew\\Model';
            }
        
            # Dependencies
            $database = self::database();

            # Instantiation and storage
            $registry->$class_name = new $class_name($database, $table_name);
        }
        
        return $registry->$class_name;
    }

    /**
     * Obtains a library instance of the specified class.
     *
     * @param string $class_name Name of the library class
     * @param mixed $arguments One or more arguments for the constructor of the library
     * @return Object An instance of the required Library
     */
    public static function library($class_name, $arguments = null)
    {
        $library = null;

        try {
            $library = self::get(self::$config->app_namespace . '\\libs\\' . $class_name);
        } catch (\Exception $e) {
            if (!$library) {
                $library = self::get(__NAMESPACE__ . '\\libs\\' . $class_name);
            }
        }

        return $library;
    }

    /**
     * Obtains an instance of the database access object.
     * 
     * This function retrieves an instance of the current Database access class,
     * usually and by default PewDatabase.
     * 
     * The $config parameter specifies the connection configuration to use.
     *
     * @param string $config The configuration name
     * @return Object An instance of the database access object
     * @throws RuntimeException If database use is disabled
     */
    public static function database($config = null)
    {
        $registry = Registry::instance();

        if (!isset($registry->Database)) {
            # load app/config/database.php
            if (file_exists(self::$config->app_folder . DS . 'config' . DS . 'database.php')) {
                self::$config->database_config = include self::$config->app_folder . DS . 'config' . DS . 'database.php';
            }

            $db_config = self::$config->database_config;
            $use_db = $db_config['use'];

            if ($use_db !== false) {
                $use = is_string($config) ? $config : (!is_string($use_db) ? 'default' : $use_db);
                

                if (isset($db_config[$use])) {
                    $registry->Database = new libs\Database($db_config[$use]);
                } else {
                    throw new \RuntimeException("Database is disabled.");
                }                
            }
        }

        return $registry->Database;
    }
    
    /**
     * Retrieves and initialises the Request object for the current request.
     * 
     * @param string $uri_string A list of slash-separated segments.
     * @return Request The initialised request object
     */
    public static function request($uri_string = null)
    {
        $registry = Registry::instance();

        if (!isset($registry->Request)) {
            # instantiate the request object
            $registry->Request = $request = new libs\Request();
        }

        return $registry->Request;
    }

    /**
     * Retrieves and initialises a Router object.
     * 
     * @param string $uri_string A list of slash-separated segments.
     * @return Router The initialised request object
     * @throws Exception When the class does not exist.
     */
    public static function router($uri_string = null)
    {
        $registry = Registry::instance();

        if (!isset($registry->Router)) {
            $routes = [];
            # fetch the routes configuration

            // load app/config/routes.php
            if (file_exists(self::$config->app_folder . DS . 'config' . DS . 'routes.php')) {
                $routes = include self::$config->app_folder . DS .'config' . DS . 'routes.php';
            }

            # instantiate the router object
            $router = new libs\Router($routes);
            $router->default_controller(self::$config->default_controller);
            $router->default_action(self::$config->default_action);

            $registry->Router = $router;
        }

        return $registry->Router;
    }

    /**
     * Get or instance an authentication object
     * 
     * @return object The authentication object
     */
    public static function auth()
    {
        $registry = Registry::instance();

        if (!isset($registry->Auth)) {
            $database = self::database();
            $session = self::session();
            // if (!$database || !$session) {
            //     throw new RuntimeException('Auth requires Database and Session providers');
            // }
            $registry->Auth = new Auth($database, $session);
        }

        return $registry->Auth;
    }

    /**
     * Get or instance a log object
     * 
     * @return object The log object
     */
    public static function log()
    {
        $registry = Registry::instance();

        if (!isset($registry->Log)) {
            $registry->Log = new PewLog(self::$config->log_level);
        }

        return $registry->Log;
    }

    /**
     * Get or instance a session object
     * 
     * @return object The session object
     */
    public static function session()
    {
        $registry = Registry::instance();

        if (!isset($registry->Session)) {
            $registry->Session = new \pew\libs\Session();
        }

        return $registry->Session;
    }

    /**
     * Gets a view and initialises it.
     *
     * Call this method without arguments to retrieve the base view. Use a key
     * to create and initialize a view conforming to the following parameters:
     *
     * $key = 'default' -> the base folder is the views folder of the framework
     * $key = !null     -> the view is a new instance
     *
     * @return View A view object
     */
    public static function view($key = '')
    {
        $registry = Registry::instance();

        $view_key = "View_$key";

        if (!isset($registry->{$view_key})) {
            $registry->{$view_key} = new View(self::$config->app_folder . DIRECTORY_SEPARATOR . 'views');
        }

        return $registry->{$view_key};
    }
}
