<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * package sys
 */

/**
 * The Log class manages debugging messages.
 * 
 * To use the Log class, call Log::in() with one or two String parameters
 * wherever in your application. To display the logged values, use Log::out()
 * in a view or layout, or modify app.class.php to output the Log after the view
 * is rendered.
 * 
 * Use Log::to_file and provide a filename and an optional directory to save the
 * contents of the log to a file.
 *
 * The Log::out() method prints the log event if DEBUG is 0. On the other hand,
 * the Debug element (available by default) is aware of the DEBUG constant and
 * will only work if DEBUG is greater than 0.
 *
 * @version 0.4 16-may-2012
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class Log
{
    /**
     * Stores the entries to the log, in numeric or string indexes.
     *
     * @var array
     * @access protected
     */
    protected $_log = array();

    public function __construct()
    {
        $this->_log = array();
    }

    /**
     * Appends entries to the log.
     *
     * @param string $value The value to be logged
     * @param string $title A title for the logged value
     * @param array $caller_data An associative array with function, line and file indexes
     * @return void
     * @access public
     */
    public function in($value, $title = null, array $caller_data = null)
    {
        # Current timestamp
        $time = microtime(true);
        
        # Function name, file name and line number which called in()
        $backtrace = debug_backtrace();
        $caller_info = $backtrace[0];
        $caller_info['function'] = isset($backtrace[1]) ? $backtrace[1]['function'] : 'main';
        $caller_info = array_merge($caller_info, (array) $caller_data);

        $caller['file'] = $caller_info['file'];
        $caller['line'] = $caller_info['line'];
        $caller['function'] = $caller_info['function'];

        # Collect log entry info
        $entry = compact('time', 'caller', 'value', 'title');

        # Add entry to log
        $this->_log[] = $entry;
        
        # Return entry
        return $entry;
    }
    
    /**
     * Prints and returns the contents of the log.
     *
     * @param  bool $print If false, the output is not printed
     * @return string|bool Returns false when there is no output
     * @access public
     */
    function out($print = true)
    {
        if ($this->_log && count($this->_log)) {

            ob_start();

            echo '<dl id="asterisc-log">' . PHP_EOL;
            foreach ($this->_log as $key => $value) {
                echo "<dt>Logged: <em>{$value['title']} ({$value['time']})</em></dt>" . PHP_EOL;
                echo '<dd>' . PHP_EOL;
                var_dump($value['value']);
                echo '</dd>' . PHP_EOL;
            } // foreach _log
            echo '</dl> <!-- asterisc-log -->' . PHP_EOL;
            
            $log_contents = ob_get_contents();
            ob_end_clean();

            if ($print) {
                echo $log_contents;
            }

            return $log_contents;
        }  else {
            return false;
        }
    }
    
    function to_file($filename, $location = null)
    {
        if ($this->_log) {
            if (!$location) {
                $location = ROOT . 'logs';
            } else {
                $location = rtrim($location, "/\\") . DIRECTORY_SEPARATOR;
            }
            
            if (!file_exists($location)) {
                if (!mkdir($location) || !file_exists($location)) {
                    return false;
                }
            }
            
            $log_contents = "Log started on " . date(DATE_RFC822) . PHP_EOL;
            $log_contents .= "- - - - -" . PHP_EOL;
            
            foreach ($this->_log as $k => $entry) {
                $log_contents .= $entry['time'] . ' ' . ($entry['title'] ? : '') . PHP_EOL;
                $log_contents .= $entry['caller']['file'] . ':' . $entry['caller']['line'] . ' >> ' . $entry['caller']['function'] . PHP_EOL;
                $log_contents .= print_r($entry['value'], true) . PHP_EOL;
                $log_contents .= "- - - - -" . PHP_EOL;
            }
            
            file_put_contents($location . DIRECTORY_SEPARATOR . $filename, $log_contents);
        }
    }
    
    /**
     * Prints and returns the contents of the $_SESSION variable.
     * 
     * @param  boolean $print If false, the log is not printed
     * @return string|bool The contents of the log, or false if there is no session
     */
    function session($print = true)
    {
        # Check if the session is in use
        if (isset($_SESSION) and count($_SESSION > 0)) {
            
            ob_start();

            echo '<dl id="session-log">' . PHP_EOL;
            foreach ($_SESSION as $key => $value) {
                echo "<dt>Logged: <em>{$key}</em></dt>" . PHP_EOL;
                echo '<dd>' . PHP_EOL;
                var_dump($value);
                echo '</dd>' . PHP_EOL;
            } // foreach _log
            echo '</dl> <!-- asterisc-log -->' . PHP_EOL;
            
            $log_contents = ob_get_contents();
            ob_end_clean();

            if ($print){
                echo $log_contents;
            }

            return $log_contents;
        }
        
        return false;
    }

    /**
     * Remove the previously logged entries.
     * 
     * @return int Amount of log entries removed
     */
    function clear()
    {
        # Count the amount of items that will be removed
        $count = $this->count();

        # Reset the log
        $this->_log = array();

        return $count;
    }

    /**
     * Count the number of log entries.
     * 
     * @return int Amount of entries currently in the log
     */
    function count()
    {
        return count($this->_log);
    }
}
