<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

/**
 * A basic configuration storage.
 *
 * The cfg() function can be called with a single parameter to retrieve its 
 * value from the storage, or with two parameters to save the value in the
 * storage.
 * 
 * If the $key parameter is boolean true, the return value is the full content
 * of the storage.
 * 
 * @param int|string $key Key to set
 * @param mixed $value Value to set
 * @return mixed
 * @version 0.1 04-jan-2012
 * @author ifcanduela <ifcanduela@gmail.com>
 */
function cfg($key, $value = null, $default = null)
{
    # static storage
    static $_config_values = array();
    
    # return all values in special case
    if ($key === true) {
        return $_config_values;
    }
    
    # return null if key is invalid
    if (!is_string($key) && !is_int($key)) {
        return null;
    }

    # if there is no value argument...
    if (is_null($value)) {
        # and the key was created
        if (array_key_exists($key, $_config_values)) {
            # return the value
            return $_config_values[$key];
        } else {
            # if the key does not exist, create it and assign $default/null
            return $_config_values[$key] = $default;
        }
    } else {
        # assign the value to the key
        $_config_values[$key] = $value;
        # and return it
        return $value;
    }
}

/**
 * A handy wrapper for print_r.
 *
 * The pr() function calls print_r with the $data parameter, wrapping the call
 * inside &lt;pre> tags and preprending the optional $title parameter to help
 * identify the output. It will not produce HTML if the script is running from 
 * the console.
 *
 * Note that this function is not aware of the DEBUG constant, i.e. it will 
 * always output.
 * 
 * @param mixed $data The data to be printed
 * @param string $title Optional title of the printed data
 * @return void
 * @version 0.3 24-oct-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 */
function pr($data, $title = null)
{
    # acquire formatted $data
    $echo = print_r($data, true);
    
    if (defined('STDIN')) {
        # insert $title if it's provided
        if ($title) {
            $echo = "$title: $echo";
        }
    } else {
        # insert $title if it's provided
        if ($title) {
            $echo = "<em>$title</em>: $echo";
        }
        # wrap the text in <pre> tags
        $echo = "<pre>$echo</pre>";
    }
    
    # echo everything
    echo $echo;
}

/**
 * Triggers an E_USER_ERROR message and shows a simple trace.
 * 
 * @param string $message A descriptive error message
 * @param int $level Error level, E_USER_ERROR by default
 * @return void
 * @version 0.1 28-jul-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @see http://php.net/debug_backtrace
 */
function pew_exit($message, $level = E_USER_ERROR)
{
    $debug_backtrace = debug_backtrace();
    # get the caller of the function that called pew_exit()
    $callee = $debug_backtrace[0];
    # trigger the appropriate error
    trigger_error("ERROR: $message in {$callee['file']} on line {$callee['line']}", $level);
    # print the complete backtrace
    debug_print_backtrace();
}

/**
 * Setups and returns a timer to compute the script execution time.
 *
 * @param bool $partial If true, the value returned is the time passed since the
 *                      last call to the function
 * @return float Seconds elapsed since the first or last call to the function
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.1 25-jun-2011
 * @see http://php.net/manual/en/function.microtime.php
 */
function get_execution_time($partial = false)
{
    static $microtime_start = null;
    static $microtime_last = null;
    
    $microtime = microtime(true);
    
    if ($microtime_start === null)
    {
        $microtime_start = $microtime_last = $microtime;
        return 0.0;
    }
    
    $microtime_partial = $microtime - $microtime_last;
    $microtime_last    = $microtime;
    
    if (!$partial) {
        return $microtime - $microtime_start; 
    } else {
        return $microtime_partial;
    }
}

/**
 * Applies a text sanitization filter to a string.
 *
 * This function calls filter_var with FILTER_SANITIZE_MAGIC_QUOTES if the
 * parameter is a string. If it's not a string, it return the parameter
 * unmodified.
 *
 * @param string $string The string to sanitize
 * @return string The sanitized string
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.1 24-oct-2011
 * @see http://php.net/manual/en/function.filter-var.php
 */
function sanitize($string)
{
    if (is_string($string)) {
        return filter_var($string, FILTER_SANITIZE_MAGIC_QUOTES);
    } else {
        return $string;
    }
}

/**
 * Escapes form data in a recursive way.
 * 
 * @param array $post The data from the $_POST array
 * @return array The properly-escaped data
 * @version 0.1 27-mar-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @see http://php.net/manual/en/function.stripslashes.php
 */
function clean_array_data($post)
{
    $post = is_array($post)
          ? array_map('clean_array_data', $post)
          : stripslashes($post);

    return $post;
}

/**
 * Cleans a string using basic PHP functions.
 * 
 * @param string $evil_string A string to clean up
 * @return string The resulting string
 * @version 0.1 27-oct-2011
 * @author ifcanduela <ifcanduela@gmail.com> 
 * @see http://php.net/manual/en/function.strip_tags.php
 * @see http://php.net/manual/en/function.stripslashes.php
 * @see http://php.net/manual/en/function.htmlentities.php
 * @see http://php.net/manual/en/function.filter_var.php
 */
function pew_clean_string($evil_string)
{
    $no_tags = strip_tags($evil_string);
    $no_slashes = stripslashes($no_tags);
    $no_quotes = filter_var($no_slashes, FILTER_SANITIZE_MAGIC_QUOTES);
    $clean_string = htmlentities($no_quotes, ENT_QUOTES, 'UTF-8');

    return $clean_string;
}
 
/**
 * Returns a value from an array (discarding the array itself).
 *
 * This function is useful as a workaround for the unavailability of unnamed array
 * dereferencing in PHP 5.3:
 *
 *   $my_desired_value = func_that_returns_array()[$index];
 *
 * That would not work. You just do this:
 *
 *   $my_desired_value = array_index(func_that_returns_array(), $index);
 * 
 * @param array $array The array data
 * @param mixed $index The integer or string index to retrieve
 * @param bool $strict Whether or not to throw an InvalidArgumenException
 *                     if the index does not exist
 * @throws InvalidArgumentException When $index is not found and $strict is true
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.1 24-oct-2011
 */
function deref(array $array, $index, $strict = false)
{
    if (!array_key_exists($index, $array)) {
        if ($strict) {
            throw new InvalidArgumentException("Array key does not exist: [$index]");
        } else {
            return null;
        }
    }
    
    return $array[$index];
}

/**
 * Isolate values from an array according to a pattern.
 *
 * This function accepts an array and a list of filtering atoms (in string or
 * array form) and uses the atoms to progressively scan the the keys in
 * successive dimensions of the array, discarding the indexes that do not
 * conform to the atom provided for the dimension.
 * 
 * Atom strings are of the form '#:$:literal:0'.
 *
 * The available atom types are:
 * * # or #i: matches any integer index
 * * $ or #s: matches any string index
 * * any other atom is taken as a literal value and will match indexes that
 *   equal the atom; this matching is not strict: index 1 will match atom '1'.
 *
 * @param array $data The array to be filtered
 * @param mixed $filter A string or array with the filtering atoms
 * @return array The array with the matching elements
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.4 22-jul-2011
 */
function array_reap($data, $filter)
{
    # if $filter is a string, divide and acquire filter atoms
    if (is_string($filter)) {
        $filters = explode(':', trim($filter, ':'));
    # if $filter was already an array, go ahead
    } elseif (is_array($filter)) {
        $filters = $filter;
    # any other possibility is an error
    } else {
        return null;
    }
    
    # if there are no remaining filters, $data complies with the rules
    if (count($filters) == 0)
        return $data;
    
    # if $data is an object, get its properties
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
    
    # by this point, $data must be an array
    if (!is_array($data))
        return null;
    
    # get the current filter
    $f = array_shift($filters);
    
    # scan the data array
    foreach ($data as $key => $value) {
        # assume recursive calls won't be necessary
        $reap = false;
        
        switch ($f) {
            case '#':
            case '#i': 
                # match any number
                $reap = is_numeric($key);
                break;
            case '#s':
            case '$':
                # match any string
                $reap = is_string($key);
                break;
            default:
                # match specific value
                $reap = ("$key" === "$f");
                break;
        }
        
        if ($reap) {
            $function_name = __FUNCTION__;
            # if the value matched, call recursively to reap()
            $data[$key] = $function_name($data[$key], $filters);
            # if the result is empty, discard it
            if (is_null($data[$key]) || (is_array($data[$key]) && count($data[$key]) === 0)) {
                unset($data[$key]);
            }
        } else {
            # if the key didn't match, discard it
            unset($data[$key]);
        }
    }
    
    return $data;
}

/**
 * Collect all non-array values of a multi-dimensional array.
 *
 * The flatten() function collects string, numeric an boolean values from an
 * array and returns an indexed array with those values. Keys are not kept.
 * This function is useful to simplify the results from array_reap().
 *
 * @param array $data The array to be flattened
 * @return array The array with the scalar values
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.2 22-jul-2011
 */
function array_flatten($data)
{
    # store results here
    $flat = array();
    
    # loop through the $data array
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $function_name = __FUNCTION__;
            # if value's an array, merge current elements and value's flattened result
            $flat = array_merge($flat, $function_name($value));
        } else {
            # if $value is a scalar value, append it to the results
            $flat[] = $value;
        }
    }
    
    return $flat;
}

/**
 * Convert an array into an XML structure.
 * 
 * @see http://stackoverflow.com/a/5965940/1007072
 */
function array_to_xml(array $data, &$xml, $root_name = 'root')
{
    if (is_string($xml)) {
        $xml = new SimpleXMLElement('<' . $root_name . '></' . $root_name . '>');
    }
    
    foreach ($data as $k => $v) {
        if (is_array($v)) {
            if (is_numeric($k)) {
                $child = $xml->addChild($root_name);
                array_to_xml($v, $child, $root_name);
            } else {
                $child = $xml->addChild($k);
                array_to_xml($v, $child, $k);
            }
        } else {
            $xml->addAttribute($k, $v);
        }
    }
}

/**
 * A quick way of converting file names to class names.
 * 
 * @param string $file_name The file name, without extension
 * @return string The properly-cased class name
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.2 9-mar-2011
 */
function file_name_to_class_name($file_name)
{
    #obtain the words in the file name
    $words = explode('_', $file_name);
    
    # use an anonymous function to upper-case-first every word in the array
    array_walk($words, function(&$word) {
        # convert the word to upper-case
        $word = ucfirst($word);
    });
    
    # return the words
    return join('', $words);
}

/**
 * A quick way of converting file names to class names.
 *
 * Alias of file_name_to_class_name
 * 
 * @param string $file_name The file name, without extension
 * @return string The properly-cased class name
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.1 28-apr-2012
 */
function f2c($file_name)
{
    return file_name_to_class_name($file_name);
}

/**
 * A quick way of converting class names to file names.
 * 
 * @param string $class_name The came-case class name
 * @return string The lower-case and underscore-separated file name
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.1 10-mar-2011
 */
function class_name_to_file_name($class_name)
{
    $file_name = preg_replace('/([A-Z])/', '_$1', $class_name);
    return strtolower(trim($file_name, '_'));
}

/**
 * A quick way of converting class names to file names.
 *
 * Alias of class_name_to_file_name
 * 
 * @param string $class_name The came-case class name
 * @return string The lower-case and underscore-separated file name
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.1 28-apr-2012
 */
function c2n($class_name)
{
    return class_name_to_file_name($class_name);
}

/**
 * Sends a 302-redirect http header that points to the application url passed
 * as parameter, prepending the URL constant to it.
 *
 * This function stops the execution of the current script.
 *
 * @param string $url The target address, in the form of controller/action/params
 * @return void
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.3 10-nov-2011
 */
function redirect($url)
{
    $url = ltrim($url, '/');
    header('Location: ' . URL . $url);
    exit(302);
}

/**
 * Makes sure the folders in a slash-delimited path exist.
 *
 * This is useful when mkdir() does not support the $recursive parameter,
 * although the framework is developed with PHP 5.3 in mind.
 *
 * @param string $path A single or multi-folder path
 * @return bool Returns false on error, true otherwise
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.2 15-jun-2011
 */
function check_dirs($path)
{
    # Normalize de directory separators
    $path = str_replace(array(DIRECTORY_SEPARATOR, '\\'), DIRECTORY_SEPARATOR, $path);
    
    if (file_exists($path))
        return true;
    
    # Obtain an array with from the folder string and filter empty elements
    $dirs = explode(DIRECTORY_SEPARATOR, $path);
    $dirs = array_filter($dirs);
    
    # If there are no folders, leave it be
    if (count($dirs) == 0) {
        return false;
    }
    
    $cp = '';
    
    while (count($dirs) > 0) {
        # Aggregate the folders to form the current path
        $cp .= array_shift($dirs) . DIRECTORY_SEPARATOR;
        
        if (!is_dir($cp)) {
            # If the folder does not exist, attempt to create it
            if (!mkdir($cp)) {
                # Return false on error
                return false;
            }
        }
    }
    
    return true;
}

/**
 * Utility function to create a URL-friendly string by cleaning ugly characters.
 *
 * Slugifying a string will remove any non-alpha-numeric characters (leaving
 * only A to Z, a to z, 0 to 9 and -), replace whitespace with dashes and then
 * normalize the string to only 1 consecutive dash.
 *
 * @param string $str The string to slugify
 * @return string The slug
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.2 9-mar-2011
 */
function slugify($str)
{
    # strip the string from URL-unfriendly characters
    $str = preg_replace('/[^a-zA-Z0-9 -]/', '', $str);
    
    # transform spaces into dashes and convert to lowercase
    $str = strtolower(str_replace(' ', '-', trim($str)));
    
    # reduce consecutive dashes to a single dash
    $str = preg_replace('/-+/', '-', $str);
    
    return $str;
}

/**
 * Utility function to convert dashes and spaces to underscores.
 * 
 * @param string $str The string to trasnform
 * @return string The modified string
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.1 18-mar-2011
 */
function to_underscores($str)
{
    return str_replace(array(' ', '-'), '_', $str);
}

/**
 * A quick way to print the filesystem root directory or any file below it.
 * 
 * If the framework files reside in C:\htdocs\pewexample, this call
 *     root('app\libs\my_lib.php');
 * will print
 *     C:\htdocs\pewexample\app\libs\my_lib.php
 * 
 * @param string $path A path to include in the output
 * @param bool $print Whether to print the path or not - default is true
 * @return string The resulting path
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.4 22-jul-2011
 */
function root($path = '', $print = true)
{
    if ($print) echo ROOT . str_replace('/', DIRECTORY_SEPARATOR, $path);
    return ROOT . str_replace('/', DIRECTORY_SEPARATOR, $path);
}

/**
 * Prints an absolute URL, having the location of the site as base URL.
 *
 * If the site is hosted at http://www.example.com/pewexample, the call
 *     url('www/css/styles.css');
 * will print
 *     http://www.example.com/pewexample/www/css/styles.css.
 * 
 * @param string $url A string to print after the server and path
 * @param bool $print Whether to print the url or not - default is true
 * @return string The resulting url
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.3 19-mar-2011
 */
function url($url = '', $print = true)
{
    if ($print) echo URL . $url;
    return URL . $url;
}

/**
 * Prints an absolute URL, having the location of the assets folder as base URL.
 *
 * If the site is hosted at http://www.example.com/pewexample, the call
 *     www('css/styles.css');
 * will print
 *     http://www.example.com/pewexample/www/css/styles.css.
 *
 * This function is necessary if the location of the assets folder (www) is
 * different from the default.
 * 
 * @param string $url A string to print after the server, path and www location.
 * @param bool $print Whether to print the url or not - default is true
 * @return string The resulting url
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.1 06-oct-2011
 */
function www($url = '', $print = true)
{
    if ($print) echo WWW . $url;
    return WWW . $url;
}

/**
 * Prints some useful framework configuration information.
 * 
 * @return void
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.2 05-oct-2011
 */
function print_config()
{
    echo "ROOT   = " . ROOT . PHP_EOL;
    echo "SYSTEM = " . SYSTEM . PHP_EOL;
    echo "APP    = " . APP. PHP_EOL;
    echo "URL    = " . URL. PHP_EOL;
}

/**
 * Gets the currently logged-in user data, if any.
 * 
 * Commonly used in this way: echo user()->username;
 *
 * @return stdClass|boolean An object with the user info if there's a user 
 *                          logged in, boolean false otherwise.
 * @author ifcanduela <ifcanduela@gmail.com>
 * @version 0.1 04-nov-2011
 */
function user()
{
    static $return = null;
    
    if (!isset($return)) {
        $return = false;
        
        if (class_exists('Pew') && USEAUTH) {
            $user = Pew::Get('Auth')->user();
            if (is_array($user)) {
                $return = (object) $user;
            }
        }
    }
    
    return $return;
}
