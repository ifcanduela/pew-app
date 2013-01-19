<?php

/**
 * @package sys
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';

/**
 * An object registry.
 * 
 * The Pew class is a hybrid registry/factory that contains singleton-like
 * instances of classes in the framework. It's implemented as a collection
 * of static methods that return instances of Controllers, Models and other 
 * classes.
 * 
 * @package sys
 * @author ifcanduela <ifcanduela@gmail.com>
 */
class Pew
{
    /**
     * Framework and application configuration settings.
     * 
     * @var array
     * @access protected
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
     * @access protected
     * @throws Exception
     */
    protected function __construct() { }

    /**
     * Class autoloader.
     *
     * Converts a class name into a file name and includes the file. Paths
     * must be setup elsewhere.
     * 
     * @param string $class Class name
     * @return boolean True on success, false otherwise
     */
    public static function autoload($class)
    {
        # Build a filename
        $filename = class_name_to_file_name($class) . '.class.php';

        # Check if the file exists
        if (stream_resolve_include_path($filename)) {
            # Import it
            require_once $filename;
            return true;
        }

        return false;
    }

    /**
     * Adds a path to PHP's include path.
     * 
     * @param string $path Path to add
     * @return mixed The old include path on success, false on error
     */
    public static function register_path($path)
    {
        return set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    }

    /**
     * Registers a constructor for a service.
     * 
     * @param string $service Service name
     * @param string $class_name Class name for the class that provides the service
     * @static
     */
    public static function register($service, $class_name)
    {
        if (is_string($service) && is_string($class_name)) {
            self::$_classes[$service] = $class_name;
        } else {
            throw new InvalidArgumentException("Service name and Class name must be strings");
        }
    }

    /**
     * Obtains an object of the specified class.
     * 
     * The $arguments parameter is used in the call to the class constructor.
     * 
     * @param string $index Index in the storage
     * @param mixed $arguments A single argument or an array of arguments
     * @return Object An instance of the required class
     * @access public
     * @static
     */
    public static function get($index, $arguments = array())
    {
        $registry = Registry::instance();
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
     * @access public
     * @static
     */
    public static function app($app_folder)
    {
        spl_autoload_register(array('Pew', 'autoload'));

        $registry = Registry::instance();

        $pew_config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        self::$config = new Registry();
        self::$config->import($pew_config);

        if (!isset($registry->App)) {
            // load app/config/config.php
            $app_config = include(getcwd() . DS . $app_folder . DS . 'config' . DS . 'config.php');

            // merge user config with Pew config
            self::$config->import($app_config);

            // add application path
            self::$config->app_folder = getcwd() . DS . trim(basename($app_folder), '\\/') . DS;

            // update include_path
            set_include_path(get_include_path() 
                    . PATH_SEPARATOR . self::$config->app_folder
                    . PATH_SEPARATOR . self::$config->default_folder
                    . PATH_SEPARATOR . self::$config->system_folder
                );

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
     * @param string $theClassname Name of the controller class
     * @return Object An instance of the required Controller
     * @access public
     * @static
     * @throws InvalidArgumentException When no current controller exists and no class name is provided
     */
    public static function controller($controller_name = null)
    {   
        $registry = Registry::instance();

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
            if (!class_exists($class_name)) {
                $filename = self::$config->controllers_folder . $controller_name . self::$config->class_ext;
                require_once $filename;
            }

            if (isset($registry->$class_name)) {
                # if the controller was previously instanced
                $controller = $registry->$class_name;
            } else  {
                # instance the controller
                $controller = new $class_name(self::request());
                $controller->view = self::view();

                # save the controller to the registry
                $registry->$class_name = $controller;

                # set the first controller that reaches this point as the current controller
                if (!isset($registry->CurrentRequestController)) {
                    $registry->CurrentRequestController = $controller;
                }
            }
        }

        return $controller;
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
     * @access public
     * @static
     */
    public static function model($class_name, $arguments = null)
    {
        $registry = Registry::instance();

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
            $db = self::database();
            $table_name = class_name_to_file_name(substr($class_name, 0, -5));

            # Instantiation and storage
            $registry->$class_name = new $class_name($db, $table);
        }
        
        return $registry->$class_name;
    }

    /**
     * Obtains a library instance of the specified class.
     *
     * @param string $class_name Name of the library class
     * @param mixed $arguments One or more arguments for the constructor of the library
     * @return Object An instance of the required Library
     * @access public
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
     * @access public
     * @static
     */
    public static function database($config = null)
    {
        $registry = Registry::instance();
        $use_db = self::$config->use_db;
        $db_config = self::$config->database_config;

        if (!isset($registry->PewDatabase)) {
            if ($use_db !== false) {
                $use = is_string($config) ? $config : (!is_string($use_db) ? 'default' : $use_db);
                
                if (isset($db_config[$use])) {
                    $registry->PewDatabase = new PewDatabase($db_config[$use]);
                } else {
                    throw new Exception("Database is disabled.");
                }                
            }
        }

        return $registry->PewDatabase;
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
        $registry = Registry::instance();

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
        $registry = Registry::instance();

        if (!isset($registry->Auth)) {
            $db = self::database();
            $session = self::session();
            // if (!$db || !$session) {
            //     throw new RuntimeException('Auth requires Database and Session providers');
            // }
            $registry->Auth = new Auth($db, $session);
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
            $registry->Session = new Session();
        }

        return $registry->Session;
    }

    /**
     * Gets the current view and initialises it.
     *
     * @return View A view object
     */
    public static function view($name = '') {
        $registry = Registry::instance();

        if (!isset($registry->CurrentView)) {
            $registry->CurrentView = new View;
        }

        return $registry->CurrentView;
    }

    /**
     * Retrieves registered service objects.
     * 
     * Services must be first registered with self::register. The following
     * services are registered by default: 
     * 
     *  - auth
     *  - database
     *  - log
     *  - model
     *  - request
     *  - session
     * 
     * @param  $method
     * @param  $arguments
     * @return object
     * @throw BadMethodCallException
     */
    public static function __callStatic($key, $arguments)
    {
        $registry = Registry::instance();

        if (isset($registry->$key)) {
            return $registry->$key;
        } else {
            throw new BadMethodCallException("No service configured for [$key]");
        }
    }
}
