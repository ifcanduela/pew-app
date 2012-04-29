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
 * @version 0.5 13-mar-2012
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
     * Class type constants
     */
    const CONTROLLER    = 'controller';
    const MODEL         = 'model';
    const LIBRARY       = 'library';
    const SYSTEM        = 'sys';

    /**
     * Constructor is out of bounds.
     *
     * @access protected
     */
    protected function __construct() { }

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
        if ($forced === true
        || !isset(self::$_map) && !is_array(self::$_map)) {
            self::$_map = array();
        }
    }

    /**
     * Stores an object in the registry.
     * 
     * This function does not overwrite storage indexes.
     * 
     * @param string $index The storage index
     * @param object $obj The object to store
     * @return boolean true if the object was stored, false on error
     * @access protected
     * @static
     */
    public static function Set($index, $obj)
    {
        if (is_string($index) && is_object($obj)) {
            if (!isset(self::$_map[$index])) {
                self::$_map[$index] = $obj;
                return true;
            }
        }
        
        return false;
    }

    /**
     * Obtains an object of the specified class.
     * 
     * @param string $theClassname Name of the class
     * @param string $type Either 'controller', 'model', or 'sys' (default)
     * @return Object An instance of the required class
     * @access public
     * @static
     */
    public static function Get($theClassName, $arguments = null, $type = self::SYSTEM)
    {
        self::init();
        
        # Store the lower-case class name to use as index
        $map_index = strtolower($theClassName);
        
        if (!isset(self::$_map[$map_index])) {
            if (class_exists($theClassName)) {
                if (is_array($arguments)) {
                    $reflection_class = new ReflectionClass($theClassName);
                    self::$_map[$map_index] = $reflection_class->newInstanceArgs($arguments);
                } elseif (is_null($arguments)) {
                    self::$_map[$map_index] = new $theClassName;
                } else {
                    self::$_map[$map_index] = new $theClassName($arguments);
                }
            } else {
                return false;
            }
        }
        
        return self::$_map[$map_index];
    }

    /**
     * Obtains a controller instance of the specified class.
     * 
     * @param string $theClassname Name of the controller class
     * @return Object An instance of the required Controller
     * @access public
     * @static
     */
    public static function GetController($theClassName, $argument = null)
    {
        return self::Get($theClassName, $argument, self::CONTROLLER);
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
    public static function GetModel($theClassName, $argument = null)
    {
        # Make sure the suffix "Model" is added to the class name
        $theControllerName = rtrim($theClassName, 'Model');
        $theClassName =  $theControllerName . 'Model';
        
        $obj = self::Get($theClassName, $argument, self::MODEL);
        
        if (!$obj) {
            $obj = new Model(strtolower($theControllerName));
        }
        
        return $obj;
    }

    /**
     * Obtains a library instance of the specified class.
     *
     * @param string $theClassname Name of the libraryclass
     * @return Object An instance of the required Library
     * @access public
     * @static
     */
    public static function GetLibrary($theClassName, $argument = null)
    {
        return self::Get($theClassName, $argument, self::LIBRARY);
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
    public static function GetDatabase($config = null)
    {
        $error = false;
        
        if (USEDB !== false) {
            if (!isset(self::$_map[DATABASE_CLASS_NAME])) {
                if (defined('DATABASE_CONFIGURATION')) {
                    require DATABASE_CONFIGURATION;
                } else {
                    require APP . 'config/database_configuration.php';
                }
                
                $dbc = new DatabaseConfiguration();
                
                $use = is_string($config) ? $config : (!is_string(USEDB) ? 'default' : USEDB);
                
                if (isset($dbc->config[$use])) {
                    $database_class_name = DATABASE_CLASS_NAME;
                    self::$_map[DATABASE_CLASS_NAME] = new $database_class_name($dbc->config[$use]);
                } else {
                    $error = 'Database is not properly configured';
                }
                
            }    
        } else {
            $error = 'Database is not enabled';
        }
        
        if ($error) {
            if (DEBUG) {
                throw new Exception($error);
            } else {
                new PewError(404);
            }
        }
        
        return self::$_map[DATABASE_CLASS_NAME];
    }
    
    public static function GetRequest($uri_string)
    {
        # instantiate the request object
        $request = self::Get(REQUEST_CLASS);
        
        # configure fallback controller and action
        $request->set_default(DEFAULT_CONTROLLER, DEFAULT_ACTION);
        
        # process user-configured routes
        $url = $request->remap($uri_string);
        
        # parse the resulting URI string (throws exception on error)
        $request->parse($url);
        
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
    public static function Clean() {
        self::init(true);
    }
}
