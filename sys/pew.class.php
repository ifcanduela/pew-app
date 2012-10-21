<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

defined('DATABASE_CLASS_NAME') or define('DATABASE_CLASS_NAME', 'PewDatabase');

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
     * @var array $_map
     * @access protected
     * @static
     */
    protected static $_map = null;

    /**
     * Map of class domains and class names for "services".
     * 
     * @var array
     */
    protected static $_classes = array(
            'auth' => 'Auth',
            'database' => 'PewDatabase',
            'log' => 'PewLog',
            'model' => 'Model',
            'request' => 'PewRequest',
            'session' => 'Session',
        );
    
    /**
     * Special storage for the main controller in the current request.
     */
    const CURRENT_REQUEST_CONTROLLER = '_current_request_controller_';

    /**
     * Constructor is out of bounds.
     *
     * @access protected
     * @throws Exception
     */
    protected function __construct() { throw new BadMethodCallException("Pew cannot be instanced."); }

    /**
     * Initializes the object store.
     * 
     * @param bool $forced Set to true to unconditionally reset the store
     * @return void
     * @access protected
     * @static
     */
    protected static function init($forced = false)
    {
        if ($forced === true || !isset(self::$_map) && !is_array(self::$_map)) {
            self::$_map = array();
        }
    }

    /**
     * Checks if an object is sotred in the registry.
     * 
     * @param string $index Index to check
     * @return bool True if the index is occupied, false if it's available
     * @access public
     */
    public static function exists($index)
    {
        return array_key_exists($index, self::$_map);
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
     * @param object $obj The object to store
     * @return boolean true if the object was stored, false on error
     * @access public
     * @static
     */
    public static function set($index, $obj)
    {
        if (is_string($index) && is_object($obj)) {
            if (!isset(self::$_map[$index])) {
                self::$_map[$index] = $obj;
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
    public static function get($index, $arguments = null, $as_array = false)
    {
        self::init();

        if (self::exists($index)) {
            return self::$_map[$index];
        } else {
            if (class_exists($index)) {
                if (is_array($arguments) && !$as_array) {
                    $reflection_class = new ReflectionClass($index);
                    self::$_map[$index] = $reflection_class->newInstanceArgs($arguments);
                } elseif (is_null($arguments)) {
                    self::$_map[$index] = new $index();
                } else {
                    self::$_map[$index] = new $index($arguments);
                }
            } else {
                throw new Exception("Class $index could not be found.");
            }
        }
        
        return self::$_map[$index];
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
    public static function get_controller($class_name = null, $argument_list = null)
    {
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
            if (self::exists($class_name)) {
                # if the controller was previously instanced
                $controller = self::get($class_name);
            } else  {
                # instance the controller
                $controller = self::get($class_name, $argument_list);
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
    public static function get_model($class_name, $arguments = null)
    {
        # Make sure the suffix "Model" is added to the class name
        if (substr($class_name, -5) !== 'Model') {
            $class_name .= 'Model';
        }

        if (class_exists($class_name)) {
            $obj = self::get($class_name);
        } else {
            $table_name = class_name_to_file_name(substr($class_name, 0, -5));
            $obj = new Model($table_name);
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
    public static function get_library($class_name, $arguments = null)
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
    public static function get_database($config = null)
    {
        $database_class_name = self::$_classes['database'];

        if (self::exists($database_class_name)) {
            return self::get($database_class_name);
        } else {
            if (USEDB !== false) {
                if (defined('DATABASE_CONFIGURATION')) {
                    require DATABASE_CONFIGURATION;
                } else {
                    require APP . 'config/database_configuration.php';
                }
                
                $dbc = new DatabaseConfiguration();

                $use = is_string($config) ? $config : (!is_string(USEDB) ? 'default' : USEDB);
                
                if (isset($dbc->config[$use])) {
                    return self::get($database_class_name, $dbc->config[$use], true);
                } else {
                    throw new Exception("Database is not properly configured");
                }                
            }    
        }
        
        return self::$_map[DATABASE_CLASS_NAME];
    }
    
    /**
     * Retrieves and initialises the PewRequest object for the current request.
     * 
     * @param string $uri_string A list of slash-separated segments.
     * @return PewRequest The initialised request object
     * @throws Exception When the class does not exist.
     */
    public static function get_request($uri_string)
    {
        $class_name = self::$_classes['request'];

        if (self::exists($class_name)) {
            $request = self::get($class_name);
        } else {
            # instantiate the request object
            $request = self::get($class_name);
        
            # configure fallback controller and action
            $request->set_default(DEFAULT_CONTROLLER, DEFAULT_ACTION);
        
            # process user-configured routes
            $url = $request->remap($uri_string);
        
            # parse the resulting URI string (throws exception on error)
            $request->parse($url);
        }

        return $request;
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
        self::init(true);
    }

    /**
     * Retrieves registered service objects.
     * 
     * Services must be first registered with Pew::register. The following
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
    public static function __callStatic($method, $arguments)
    {
        if (strpos($method, '_') === false) {
            throw new BadMethodCallException("No such method in class Pew [$method]");
        }

        list($prefix, $prop) = explode('_', $method, 2);

        if (array_key_exists($prop, self::$_classes)) {
            return self::get(self::$_classes[$prop], $arguments);
        } else {
            throw new BadMethodCallException("No service configured for [$prop]");
        }
    }
}
