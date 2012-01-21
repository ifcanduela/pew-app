<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

define ('FLASHDATA', 'flash');

/**
 * A simple session management class.
 *
 * @version 0.5 03-oct-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class Session
{
    /**
     * A static variable to hold session status.
     *
     * @var boolean
     * @access protected
     */
    public $_session = false;
    
    /**
     * A static variable to hold session status.
     *
     * @var boolean
     * @access protected
     */
    public $_session_prefix = null;
    
    /**
     * The constructor initializes the server session.
     *
     * @access public
     */
    public function __construct($open = true)
    {
        $this->_session_prefix = basename(getcwd());
        
        if ($open) {
            $this->open();
        }
    }
    
    /**
     * Starts a session if none is started.
     * 
     * @access public
     */
    public function open()
    {
        $this->_session = session_id();
        
        if ("" === $this->_session) {
            $this->_session = session_start();
            
            if (!is_array($_SESSION[$this->_session_prefix])) {
                $_SESSION[$this->_session_prefix]= array();
            }
            
            return $this->_session !== "";
        }
        
        return false;
    }
    
    /**
     * Checks if there is a session open.
     * 
     * @access public
     * @return boolean Returns true if there is a session, false otherwise
     */
    public function is_open()
    {
        return session_id() !== "" && $this->_session;
    }
    
    /**
     * Closes the current session if there is one.
     * 
     * @access public
     * @static
     */
    public function close()
    {
        if ($this->is_open()) {
            session_destroy();
            $this->_session = false;
        }
    }
    
    /**
     * Stores a value in the session array.
     * 
     * @param $key string The key for the data
     * @param $value mixed The value of the data
     * @access public
     */
    public function write($key, $value)
    {
        if (!$this->is_open()) {
            $this->open();
        }
        
        $_SESSION[$this->_session_prefix][$key] = $value;
    }
    
    /**
     * Retrieves a value from the session array, and returns another value if
     * the key is not present.
     * 
     * @param $key string The key for the data
     * @param $alternate_value string Optional value to return if key is unset
     * @return mixed The value of the data
     * @access public
     */
    public function read($key, $alternate_value = null)
    {
        if (!$this->is_open()) {
            $this->open();
        }
        
        if (isset($_SESSION[$this->_session_prefix][$key])) {
            return $_SESSION[$this->_session_prefix][$key];
        } else {
            return $alternate_value;
        }
    }
    
    /**
     * Determines whether a key exists in the session array.
     * 
     * @param $key string The key to search
     * @return mixed True if the key is set and is not null
     *               Null if no session was started
     * @access public
     */
    public function exists($key)
    {
        if (!$this->is_open()) {
            return null;
        }
        
        return array_key_exists($key, $_SESSION[$this->_session_prefix]);
    }

    /**
     * Removes a value from the session array.
     * 
     * @param $key string The key to delete
     * @return void
     * @access public
     */
    public function delete($key)
    {
        if (!$this->is_open()) {
            $this->open();
        }
        
        unset($_SESSION[$this->_session_prefix][$key]);
    }
    
    /**
     * Sets a flash message.
     *
     * @param string $message The message to store
     * @return void
     * @access public
     */
    public function set_flash($message)
    {
        $this->write(FLASHDATA, $message);
    }
    
    /**
     * Checks if there is a flash message.
     *
     * @return bool
     * @access public
     */
    public function is_flash()
    {
        return $this->exists(FLASHDATA);
    }
    
    /**
     * Retrieves and resets the flash message.
     * 
     * @return mixed The flash message, or false if it does not exist
     * @access public
     */
    public function get_flash($prefix = '', $suffix = '')
    {
        if ($this->exists(FLASHDATA)) {
            $message = $this->read(FLASHDATA);
            $this->delete(FLASHDATA);
            return $prefix . $message . $suffix;
        } else {
            return false;
        }
    }
    
    function __set($key, $value)
    {
        if ($key === FLASHDATA) {
            $this->set_flash($value);
        } else {
            $this->write($key, $value);
        }
    }
    
    function __get($key)
    {
        if ($key === FLASHDATA) {
            return $this->get_flash();
        } else {
            return $this->read($key);
        }
    }
    
    function __isset($key)
    {
        if ($key === FLASHDATA) {
            return $this->is_flash();
        } else {
            return $this->exists($key);
        }
    }
    
    function __unset($key)
    {
        if ($key !== FLASHDATA) {
            return $this->delete($key);
        } else {
            return false;
        }
    }
}
