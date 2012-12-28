<?php

/**
 * @package sys
 */

/**
 * An object registry.
 * 
 * The Pew class is a bastard registry/factory that contains singleton-like
 * instances of classes in the framework. It's implemented as a collection
 * of static methods that return instances of Controllers and Models.
 * 
 * This class does not do file lookups. The classes must either be available or
 * be loaded by autoloading functions.
 * 
 * @version 0.6 18-may-2012
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class Pew
{
    /**
     * The object store.
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $map = array();

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
     * Special storage index for the main controller in the current request.
     */
    const CURRENT_REQUEST_CONTROLLER = '_current_request_controller_';

    /**
     * Constructor is out of bounds.
     *
     * @access protected
     * @throws Exception
     */
    protected function __construct() { }

    public static function autoload($class)
    {
        $filename = class_name_to_file_name($class) . '.class.php';

        var_dump("Searching for class $class (file = $filename");

        if (stream_resolve_include_path($filename)) {
            require_once $filename;
            return true;
        }
    }

    public static function register_path($path)
    {
        
    }

    /**
     * Checks if an object is stored in the registry.
     * 
     * @param string $index Index to check
     * @return bool True if the index is occupied, false if it's available
     * @access public
     */
    public static function exists($index)
    {
        return array_key_exists($index, self::$map);
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
     * Stores an object in the registry.
     * 
     * This function does not overwrite storage indexes.
     * 
     * @param string $index The index to use for storage
     * @param mixed $obj The item to store
     * @return boolean true if the object was stored, false on error
     * @access public
     * @static
     */
    public static function set($index, $obj)
    {
        if (is_string($index)) {
            if (!isset(self::$map[$index])) {
                self::$map[$index] = $obj;
                return $obj;
            }
        }
        
        return false;
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
        if (!self::exists($index)) {
            if (class_exists($index)) {
                $reflection_class = new ReflectionClass($index);
                self::$map[$index] = $reflection_class->newInstanceArgs($arguments);
            } else {
                throw new Exception("Class $index could not be found.");
            }
        }
        
        return self::$map[$index];
    }

    /**
     * Obtains the current instance of the Pew-Pew-Pew application.
     *
     * @param $app_folder Folder name that holds the application folders and files
     * @return App Instance of the application
     * @access public
     * @static
     */
    public static function app($app_folder)
    {
        self::log()->debug("Starting app in $app_folder");

        // load app/config/config.php
        $app_config = include(getcwd() . DS . $app_folder . DS . 'config' . DS . 'config.php');

        // merge user config with Pew config
        self::$config = array_merge(self::$config, $app_config);

        // add application path
        self::$config['app_folder'] = getcwd() . DS . trim(basename($app_folder), '\\/') . DS;

        // update include_path
        set_include_path(get_include_path() 
                . PATH_SEPARATOR . self::$config['app_folder'] 
                . PATH_SEPARATOR . self::$config['default_folder']
                . PATH_SEPARATOR . self::$config['system_folder']
            );

        var_dump(get_include_path());

        var_dump(file_exists('controller.class.php'));

        // load app/config/bootstrap.php
        if (file_exists(self::$config['app_folder'] . 'config' . DS . 'bootstrap.php')) {
            require self::$config['app_folder'] . 'config' . DS . 'bootstrap.php';
        }

        // 5. load app/config/database.php
        if (file_exists(self::$config['app_folder'] . 'config' . DS . 'database.php')) {
            self::$config['database_config'] = include self::$config['app_folder'] . 'config' . DS . 'database.php';
        }

        // 6. load app/config/routes.php
        if (file_exists(self::$config['app_folder'] . 'config' . DS . 'routes.php')) {
            self::$config['routers_config'] = include self::$config['app_folder'] . 'config' . DS . 'routes.php';
        }

        var_dump(self::$config);

        if (!self::exists('app')) {
            require __DIR__ . DS . 'app.class.php';
            self::set('app', new App($app_folder));
        }

        return self::get('app');
    }

    /**
     * Merges configuration arrays an return the resulting configuration.
     * 
     * @param array $config An array with configuration keys
     * @return object Object with configuration properties
     */
    public static function config(array $config = null)
    {
        if ($config) {
            self::$config = array_merge(self::$config, $config);
        }

        return (object) $config;
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
    public static function controller($class_name = null, $argument_list = null)
    {
        self::log()->debug("Loading controller $class_name");

        # check if the class name is omitted
        if (!isset($class_name)) {
            if (self::exists(CURRENT_REQUEST_CONTROLLER)) {
                # if exists, return the current controller
                $controller = self::get(self::CURRENT_REQUEST_CONTROLLER);
            } else {
                # if not, throw an exception
                throw new InvalidArgumentException("No controller could be retrieved");
            }
        } else {
            $filename = self::$config['controllers_folder'] . class_name_to_file_name($class_name) . self::$config['controller_ext'];
            require_once $filename;

            if (self::exists($class_name)) {
                # if the controller was previously instanced
                $controller = self::get($class_name);
            } else  {
                # instance the controller
                $controller = new $class_name(self::request());
                self::set($class_name, $controller);
                # maybe some dependency injection here (session, auth, log...)

                # set the first controller that reaches this point as the current controller
                if (!self::exists(self::CURRENT_REQUEST_CONTROLLER)) {
                    self::set(self::CURRENT_REQUEST_CONTROLLER, $controller);
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
        self::log()->debug("Loading model $class_name");
        
        # Make sure the suffix "Model" is added to the class name
        if (substr($class_name, -5) !== 'Model') {
            $class_name .= 'Model';
        }

        if (class_exists($class_name)) {
            $obj = self::get($class_name, array(self::get_database()));
        } else {
            $table_name = class_name_to_file_name(substr($class_name, 0, -5));
            $obj = new Model(self::get_database(), $table_name);
            self::set($class_name, $obj);
        }
        
        return $obj;
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
        if (!self::exists('PewDatabase')) {
            if (USEDB !== false) {
                $dbc = new DatabaseConfiguration();
                $use = is_string($config) ? $config : (!is_string(USEDB) ? 'default' : USEDB);
                
                if (isset($dbc->config[$use])) {
                    self::set('PewDatabase', new PewDatabase($dbc->config[$use]));
                } else {
                    throw new Exception("Database is disabled.");
                }                
            }

        }

        return self::get('PewDatabase');        
    }
    
    /**
     * Retrieves and initialises the PewRequest object for the current request.
     * 
     * @param string $uri_string A list of slash-separated segments.
     * @return PewRequest The initialised request object
     * @throws Exception When the class does not exist.
     */
    public static function request($uri_string = null)
    {
        if (self::exists('request')) {
            $request = self::get('request');
        } else {

            require_once __DIR__ . DS . 'pew_request.class.php';

            # instantiate the request object
            $request = new PewRequest($uri_string);
        
            # configure fallback controller and action
            $request->set_default(self::$config['default_controller'], self::$config['default_action']);
        
            # process user-configured routes
            $url = $request->remap($uri_string);
        
            # parse the resulting URI string (throws exception on error)
            $request->parse($url);
        }

        return $request;
    }

    public static function log()
    {
        if (!self::exists('log')) {
            require_once __DIR__ . DS . 'pew_log.class.php';
            self::set('log', new PewLog(self::$config['log_level']));
        }

        return self::get('log');
    }

    /**
     * Resets the object store.
     *
     * This method will only reset the registry, it will not delete the objects
     * previously created. Existing references will continue to work. This will
     * allow to create objects different from the old ones.
     *
     * @return void
     * @access public
     * @static
     */
    public static function clean()
    {
        self::$map = array();
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
        if (self::exists($key, self::$map)) {
            return self::get($key);
        } else {
            throw new BadMethodCallException("No service configured for [$key]");
        }
    }
}
