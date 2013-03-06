<?php

namespace pew;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';

/**
 * An object factory.
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
     * @var array
     * @static
     */
    protected static $config = array();

    /**
     * @var array Paths for class autoloading
     */
    protected static $paths = array();

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
     * @static
     */
    public static function get($index, $arguments = array())
    {
        $registry = libs\Registry::instance();
        if (!isset($registry->$index)) {
            if (class_exists($index)) {
                $reflection_class = new ReflectionClass($index);
                $registry->$index = $reflection_class->newInstanceArgs($arguments);
            } else {
                throw new Exception("Class $index could not be found.");
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
     * @static
     */
    public static function app($app_folder, $config_file = 'config')
    {
        # Include the Autoloader definition
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'Autoloader.php';

        $appLoader = new Autoloader($app_folder, dirname(realpath($app_folder)));
        $appLoader->register();
        
        $pewLoader = new Autoloader('pew', dirname(__DIR__));
        $pewLoader->register();
        
        $registry = libs\Registry::instance();

        $pew_config = require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        self::$config = new libs\Registry();
        self::$config->import($pew_config);

        if (!isset($registry->App)) {
            // load app/config/config.php
            $app_config = include getcwd() . DS . $app_folder . DS . 'config' . DS . $config_file . '.php';

            // merge user config with Pew config
            self::$config->import($app_config);

            // add application namespace and path
            $app_folder_name = trim(basename($app_folder));
            self::$config->app_namespace = '\\' . $app_folder_name;
            self::$config->app_folder = realpath($app_folder);

            var_dump(self::$config->app_namespace, self::$config->app_folder);

            // load app/config/bootstrap.php
            if (file_exists(self::$config->app_folder . 'config' . DS . 'bootstrap.php')) {
                require self::$config->app_folder . 'config' . DS . 'bootstrap.php';
            }

            // load app/config/database.php
            if (file_exists(self::$config->app_folder . 'config' . DS . 'database.php')) {
                self::$config->database_config = include self::$config->app_folder . 'config' . DS . 'database.php';
            }

            // load app/config/routes.php
            if (file_exists(self::$config->app_folder . 'config' . DS . 'routes.php')) {
                self::$config->routes = include self::$config->app_folder . 'config' . DS . 'routes.php';
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
            self::$config->import = $config;
        }

        return self::$config;
    }

    /**
     * Obtains a controller instance of the specified class.
     * 
     * @param string $controller_name Name of the controller class
     * @param Request $request a Request object
     * @return Object An instance of the required Controller
     * @static
     * @throws InvalidArgumentException When no current controller exists and no class name is provided
     */
    public static function controller($controller_name = null, \pew\libs\Request $request)
    {   
        $registry = libs\Registry::instance();

        # check if the class name is omitted
        if (!isset($controller_name)) {
            if (isset($registry->CurrentRequestController)) {
                # if exists, return the current controller
                return $registry->CurrentRequestController;
            } else {
                # if not, throw an exception
                throw new InvalidArgumentException("No controller could be retrieved");
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
     * @param string $theClassname Name of the model class, with or without
     *        the 'Model' suffix
     * @return Object An instance of the required Model
     * @static
     */
    public static function model($class_name)
    {
        $registry = libs\Registry::instance();

        # Make sure the suffix "Model" is added to the class name
        if (substr($class_name, -5) !== 'Model') {
            $class_name .= 'Model';
        }

        # Check that the model has not been previously instantiated
        if (!isset($registry->$class_name)) {
            # Instantiate Model if the derived class is not available
            if (!class_exists($class_name)) {
                $class_name = 'Model';
            }
        
            # Dependencies
            $database = self::database();
            $table_name = class_name_to_file_name(substr($class_name, 0, -5));

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
     * @static
     */
    public static function library($class_name, $arguments = null)
    {
        return self::get($class_name, $arguments);
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
     * @static
     * @throws RuntimeException If database use is disabled
     */
    public static function database($config = null)
    {
        $registry = libs\Registry::instance();

        if (!isset($registry->Database)) {
            $use_db = self::$config->use_db;
            $db_config = self::$config->database_config;

            if ($use_db !== false) {
                $use = is_string($config) ? $config : (!is_string($use_db) ? 'default' : $use_db);
                
                if (isset($db_config[$use])) {
                    $registry->Database = new Database($db_config[$use]);
                } else {
                    throw new RuntimeException("Database is disabled.");
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
     * @throws Exception When the class does not exist.
     */
    public static function request($uri_string = null)
    {
        $registry = libs\Registry::instance();

        if (!isset($registry->Request)) {
            # instantiate the request object
            $registry->Request = $request = new Request($uri_string);
            foreach (self::$config->routes as $from => $to) {
                $request->add_route($from, $to);
            }
        }

        return $registry->Request;
    }

    /**
     * Get or instance an authentication object
     * 
     * @return object The authentication object
     */
    public static function auth()
    {
        $registry = libs\Registry::instance();

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
        $registry = libs\Registry::instance();

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
        $registry = libs\Registry::instance();

        if (!isset($registry->Session)) {
            $registry->Session = new Session();
        }

        return $registry->Session;
    }

    /**
     * Gets the current view and initialises it.
     *
     * @return View A view object
     */
    public static function view()
    {
        $registry = libs\Registry::instance();

        if (!isset($registry->CurrentView)) {
            $registry->CurrentView = new View;
        }

        return $registry->CurrentView;
    }
}
