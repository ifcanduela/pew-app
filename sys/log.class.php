<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * package sys
 */

/**
 * The Log class manages debugging messages.
 * 
 * To use the Log class, call Log::in() with one or to String parameters
 * wherever in your application. To display the logged values, use Log::out()
 * in a view or layout, or modify app.class.php to output the Log after the view
 * is rendered.
 *
 * The Log::out() method prints the log event if DEBUG is 0. On the other hand,
 * the Debug element (available by default) is aware of the DEBUG constant and
 * will only work if DEBUG is greater than 0.
 *
 * @version 0.3 1-apr-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class Log
{
    /**
     * Stores the entries to the log, in numeric or string indexes.
     *
     * @var array
     * @access private
     * @static
     */
    private static $log = array();

    /**
     * Appends entries to the log.
     *
     * @param string $value The value to be logged
     * @param string $title A title for the logged value
     * @return void
     * @access public
     * @static
     */
    public static function in($value, $title = null)
    {
        if (is_string($title)) {
            self::$log[$title] = $value;
        } else {
            self::$log[] = $value;
        }
    }
    
    /**
     * Prints the contents of the log.
     *
     * @return void
     * @access public
     * @static
     */
    static function out()
    {
        if (self::$log) {
            echo '<dl id="asterisc-log">' . PHP_EOL;
            foreach (self::$log as $key => $value) {
                echo '<dt>Logged: <em>' . $key . '</em></dt>' . PHP_EOL;
                echo '<dd>' . PHP_EOL;
                if (is_array($value) || is_object($value)) {
                    echo '<dl>' . PHP_EOL;
                    foreach ($value as $subkey => $subvalue) {
                        echo '<dt><em>' . $subkey . '</em></dt>' . PHP_EOL;
                        echo '<dd>';
                        pr($subvalue);
                        echo '</dd>' . PHP_EOL;
                    } // foreach $value
                    echo '</dl>';
                } else {
                    print_r($value);
                } // else ! obj
                echo '</dd>' . PHP_EOL;
            } // foreach $log
            echo '</dl> <!-- asterisc-log -->' . PHP_EOL;
        } // if $log
    }
    
    static function session()
    {
        if (defined('USESSESSION') and USESSESION) {
            
            echo '<dl id="asterisc-log">' . PHP_EOL;
            foreach ($_SESSION[Pew::Get('Session')->_session_prefix] as $key => $value) {
                echo '<dt>Logged: <em>' . $key . '</em></dt>' . PHP_EOL;
                echo '<dd>' . PHP_EOL;
                if (is_array($value) || is_object($value)) {
                    echo '<dl>' . PHP_EOL;
                    foreach ($value as $subkey => $subvalue) {
                        echo '<dt><em>' . $subkey . '</em></dt>' . PHP_EOL;
                        echo '<dd>';
                        pr($subvalue);
                        echo '</dd>' . PHP_EOL;
                    } // foreach $value
                    echo '</dl>';
                } else {
                    print_r($value);
                } // else ! obj
                echo '</dd>' . PHP_EOL;
            } // foreach $log
            echo '</dl> <!-- asterisc-log -->' . PHP_EOL;
        } // if $log
    }
}