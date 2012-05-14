<?php

/**
 * Function that loads classes from the global include path
 * 
 * This should be used to autoload PEAR clases by adding the PEAR folder to
 * the include path in php.ini.
 * 
 * @param string $ClassName The class to be loaded
 * @return bool True if the class file is found, false otherwise
 * @version 0.1 30-nov-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 */
function pear_autoload($ClassName)
{
    static $include_paths = '';
    
    if ($include_paths === '') {
        $include_paths = explode(PATH_SEPARATOR, get_include_path());
    }
    
    $result = false;
    $file_name = str_replace('_', DIRECTORY_SEPARATOR, $ClassName) . '.php';
    
    foreach ($include_paths as $path) {
        if (file_exists($path . DIRECTORY_SEPARATOR . $file_name)) {
            require $path . DIRECTORY_SEPARATOR . $file_name;
            $result = true;
            break;
        }
    }
    
    return $result;
}

/**
 * Function that loads classes whenever they are needed
 *
 * Useful to manage the 'extends' clauses in user Controllers. To load
 * additional models and libraries following a singleton instantiation pattern,
 * Pew::Get() is preferred.
 * 
 * @param string $ClassName The class to be loaded
 * @return bool True if the class file is found, false otherwise
 * @version 0.1 10-mar-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 */
function pew_autoload($ClassName)
{
    # Guess the standardised file name of the class
    $file_name = class_name_to_file_name($ClassName);
    
    # A list of all possible locations
    $locations = array(
        # User-defined controller classes
        CONTROLLERS . $file_name . CONTROLLER_EXT,
        # User-defined model classes
        MODELS . $file_name . MODEL_EXT,
        # User-defined library classes
        LIBRARIES . $file_name . LIBRARY_EXT,
        # Pew's controller and model
        APP . $file_name . 'class.php',
        # Other framework files
        SYSTEM . $file_name . '.class.php',
        # Default controllers
        SYSTEM . 'default' . DS . 'controllers' . DS . $file_name . '.class.php',
        # Default models
        SYSTEM . 'default' . DS . 'models' . DS . $file_name . '.class.php',
    );
    
    # Search locations in order
    foreach ($locations as $location) { 
        if (file_exists($location)) { 
            # load the file
            require $location; 
            # return as soon as it's found
            return true; 
        } 
    }
    
    return false; 
}

# clear the autoload stack
spl_autoload_register(null, false);

# add pew_autoload first
spl_autoload_register('pew_autoload');
# then the PEAR-style global include path
spl_autoload_register('pear_autoload');

if (function_exists('__autoload')) {
    spl_autoload_register('__autoload');
}