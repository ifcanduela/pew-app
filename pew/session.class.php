<?php

/**
 * A simple session management class.
 *
 * @package pew
 * @author ifcanduela <ifcanduela@gmail.com>
 */
class Session
{
    /**
     * Array index for the flash messages.
     * 
     * @var string
     * @const
     */
    const FLASHDATA = '_flash_';

    /**
     * A static variable to hold session status.
     *
     * @var boolean
     * @access protected
     */
    protected $_session = false;
    
    /**
     * A static variable to hold session status.
     *
     * @var string
     * @access protected
     */
    protected $_session_prefix = null;
    
    /**
     * The constructor initializes the session prefix and opens the session.
     *
     * @param bool $open If false, the sesion is not automatically started
     * @access public
     */
    public function __construct($open = true)
    {
        $this->_session_prefix = basename(getcwd());
        
        if ($open === true) {
            $this->open();
        }
    }
    
    /**
     * Starts a session if none is started.
     * 
     * @return bool True if the session is started
     * @access public
     */
    public function open()
    {
        $this->_session = session_id();
        
        if ("" === $this->_session) {
            $this->_session = session_start();
        }
        
        if (!array_key_exists($this->_session_prefix, $_SESSION)) {
            $_SESSION[$this->_session_prefix]= array();
        }

        return $this->_session !== "";
    }

    /**
     * Get the session prefix, if any.
     * 
     * @return string
     * @access public
     */
    public function get_session_prefix()
    {
        return $this->_session_prefix;
    }
    
    /**
     * Checks if there is a session open.
     * 
     * @return boolean Returns true if there is a session, false otherwise
     * @access public
     */
    public function is_open()
    {
        return session_id() !== "" && $this->_session;
    }
    
    /**
     * Closes the current session if there is one.
     * 
     * @access public
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
        $this->write(self::FLASHDATA, $message);
    }
    
    /**
     * Checks if there is a flash message.
     *
     * @return bool
     * @access public
     */
    public function is_flash()
    {
        return $this->exists(self::FLASHDATA);
    }
    
    /**
     * Retrieves and resets the flash message.
     * 
     * @return mixed The flash message, or false if it does not exist
     * @access public
     */
    public function get_flash($prefix = '', $suffix = '')
    {
        if ($this->exists(self::FLASHDATA)) {
            $message = $this->read(self::FLASHDATA);
            $this->delete(self::FLASHDATA);
            return $prefix . $message . $suffix;
        } else {
            return false;
        }
    }
    
    public function __set($key, $value)
    {
        if ($key === self::FLASHDATA) {
            $this->set_flash($value);
        } else {
            $this->write($key, $value);
        }
    }
    
    public function __get($key)
    {
        if ($key === self::FLASHDATA) {
            return $this->get_flash();
        } else {
            return $this->read($key);
        }
    }
    
    public function __isset($key)
    {
        if ($key === self::FLASHDATA) {
            return $this->is_flash();
        } else {
            return $this->exists($key);
        }
    }
    
    public function __unset($key)
    {
        if ($key !== self::FLASHDATA) {
            return $this->delete($key);
        } else {
            return false;
        }
    }
}
