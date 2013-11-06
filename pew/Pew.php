<?php

namespace pew;

# Assorted functions
require_once __DIR__ . '/functions.php';
# Autoloader class definition
require_once __DIR__ . '/Autoloader.php';
# Registry class
require_once __DIR__ . '/libs/' . 'Registry.php';

# Autoload framework classes
(new Autoloader('pew', dirname(__DIR__)))->register();

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
     * The Pew object instance.
     *
     * @var \pew\Pew
     */
    private static $instance = null;

    /**
     * Framework and application configuration settings.
     * 
     * @var \pew\libs\Registry
     */
    protected $config = null;

    /**
     * Framework class instance storage.
     * 
     * @var \pew\libs\Registry
     */
    protected $registry = null;

    /**
     * Constructor is out of bounds.
     *
     * @throws Exception
     */
    protected function __construct()
    {
        $this->registry = Registry::instance();
        $this->config = new Registry;

        $this->init();
    }

    public static function instance()
    {
        if (!isSet(self::$instance)) {
            self::$instance = new Pew;
        }

        return self::$instance;
    }

    protected function init()
    {
        $pew_config = require_once __DIR__ . '/config.php';
        $this->config->import($pew_config);
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
    public function get($index, $arguments = [])
    {
        if (!isset($this->registry->$index)) {
            if (class_exists($index)) {
                $reflection_class = new \ReflectionClass($index);
                $this->registry->$index = $reflection_class->newInstanceArgs($arguments);
            } else {
                throw new \Exception("Class $index could not be found.");
            }
        }
        
        return $this->registry->$index;
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
    public function app($app_folder = 'app', $config_file = 'config')
    {
        if (!isset($this->registry->App)) {
            $appLoader = new Autoloader($app_folder, dirname(realpath($app_folder)));
            $appLoader->register();

            # load app/config/{$config}.php
            $app_config = include getcwd() . '/' . $app_folder . '/config/' . $config_file . '.php';

            // merge user config with Pew config
            $this->config->import($app_config);

            if (!isSet($this->config->env)) {
                $this->config->env = 'development';
            }

            # add application namespace and path
            $app_folder_name = trim(basename($app_folder));
            $this->config->app_namespace = '\\' . $app_folder_name;
            $this->config->app_folder = realpath($app_folder);
            $this->config->app_config = $config_file;

            # load app/config/bootstrap.php
            if (file_exists($this->config->app_folder . '/config/bootstrap.php')) {
                require $this->config->app_folder . '/config/bootstrap.php';
            }

            $this->registry->App = new App($app_folder);
        }

        return $this->registry->App;
    }

    /**
     * Merges configuration arrays and returns the resulting configuration.
     * 
     * @param array $config An array with configuration keys
     * @return Registry Object with configuration properties
     */
    public function config(array $config = null)
    {
        if ($config) {
            $this->config->import($config);
        }

        return $this->config;
    }

    /**
     * Obtains a controller instance of the specified class.
     * 
     * @param string $controller_name Name of the controller class
     * @param Request $request a Request object
     * @return Object An instance of the required Controller
     * @throws InvalidArgumentException When no current controller exists and no class name is provided
     */
    public function controller($controller_name = null, \pew\libs\Request $request = null)
    {
        # check if the class name is omitted
        if (!isset($controller_name)) {
            if (isset($this->registry->CurrentRequestController)) {
                # if exists, return the current controller
                return $this->registry->CurrentRequestController;
            } else {
                # if not, throw an exception
                throw new \InvalidArgumentException("No current controller could be retrieved");
            }
        } else {
            $class_name = file_name_to_class_name($controller_name);

            $app_class_name = $this->config->app_namespace . '\\controllers\\' . $class_name;
            $pew_class_name = '\\pew\\controllers\\' . $class_name;

            if (!$request) {
                $request = self::request();
            }
            
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
    public function model($table_name)
    {
        $class_name = $this->config->app_namespace . '\\models\\' . file_name_to_class_name($table_name) . 'Model';
        $class_base_name = class_base_name($class_name);

        # Check that the model has not been previously instantiated
        if (!isset($this->registry->$class_name)) {
            # Instantiate Model if the derived class is not available
            if (!class_exists($class_name)) {
                $class_name = '\\pew\\Model';
            }
        
            # Dependencies
            $database = self::database();

            # Instantiation and storage
            $this->registry->$class_name = new $class_name($database, $table_name);
        }
        
        return $this->registry->$class_name;
    }

    /**
     * Obtains a library instance of the specified class.
     *
     * @param string $class_name Name of the library class
     * @param mixed $arguments One or more arguments for the constructor of the library
     * @return Object An instance of the required Library
     */
    public function library($class_name, $arguments = null)
    {
        $library = null;

        try {
            $library = self::get($this->config->app_namespace . '\\libs\\' . $class_name);
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
    public function database($config = null)
    {
        if (!isset($this->registry->Database)) {
            if (!is_array($config)) {
                # load app/config/database.php
                if (file_exists($this->config->app_folder . '/config/database.php')) {
                    $this->config->database_config = include $this->config->app_folder . '/config/database.php';
                }

                $db_config = $this->config->database_config;
                $use_db = $db_config['use'];

                if (is_string($config)) {
                    if (!array_key_exists($config, $db_config)) {
                        throw new \RuntimeException("Database configuration preset '$config' does not exist");
                    }

                    $use = $config;
                } else {
                    $use = is_string($use_db) ? $use_db : 'default';
                }

                $config = $db_config[$use];
            }

            if (isset($config)) {
                $this->registry->Database = new libs\Database($config);
            } else {
                throw new \RuntimeException("Database is disabled.");
            }
        }

        return $this->registry->Database;
    }
    
    /**
     * Retrieves and initialises the Request object for the current request.
     * 
     * @param string $uri_string A list of slash-separated segments.
     * @return Request The initialised request object
     */
    public function request($uri_string = null)
    {
        if (!isset($this->registry->Request)) {
            # instantiate the request object
            $this->registry->Request = $request = new libs\Request();
        }

        return $this->registry->Request;
    }

    /**
     * Retrieves and initialises a Router object.
     * 
     * @param string $uri_string A list of slash-separated segments.
     * @return Router The initialised request object
     * @throws Exception When the class does not exist.
     */
    public function router($uri_string = null)
    {
        if (!isset($this->registry->Router)) {
            $routes = [];
            # fetch the routes configuration

            // load app/config/routes.php
            if (file_exists($this->config->app_folder . '/config/routes.php')) {
                $routes = include $this->config->app_folder . '/config/routes.php';
            }

            # instantiate the router object
            $router = new libs\Router($routes);
            $router->default_controller($this->config->default_controller);
            $router->default_action($this->config->default_action);

            $this->registry->Router = $router;
        }

        return $this->registry->Router;
    }

    /**
     * Get or instance an authentication object
     * 
     * @return object The authentication object
     */
    public function auth()
    {
        if (!isset($this->registry->Auth)) {
            $database = self::database();
            $session = self::session();
            // if (!$database || !$session) {
            //     throw new RuntimeException('Auth requires Database and Session providers');
            // }
            $this->registry->Auth = new Auth($database, $session);
        }

        return $this->registry->Auth;
    }

    /**
     * Get or instance a log object
     * 
     * @return object The log object
     */
    public function log()
    {
        if (!isset($this->registry->Log)) {
            $this->registry->Log = new PewLog($this->config->log_level);
        }

        return $this->registry->Log;
    }

    /**
     * Get or instance a session object
     * 
     * @return object The session object
     */
    public function session()
    {
        if (!isset($this->registry->Session)) {
            $this->registry->Session = new \pew\libs\Session();
        }

        return $this->registry->Session;
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
    public function view($key = '')
    {
        $view_key = "View_$key";

        if (!isset($this->registry->{$view_key})) {
            $prefix = in_array($this->config->views_folder{0}, ['/', '\\']) 
                          ? $this->config->root_folder
                          : $this->config->app_folder;

            $views_folder = $prefix . '/' . trim($this->config->views_folder, '/\\');

            $this->registry->{$view_key} = new View($views_folder);
        }

        return $this->registry->{$view_key};
    }

    public static function __callstatic($name, $arguments)
    {
        if (method_exists(self::$instance, $name)) {
            return self::$instance->$name($arguments);
        } else {
            throw new \RuntimeException("No method $name in class " . __CLASS__);
        }
    }
}
