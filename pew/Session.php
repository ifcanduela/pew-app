<?php

namespace pew;

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
    protected $session_id;
    
    /**
     * A static variable to hold session status.
     *
     * @var string
     * @access protected
     */
    protected $prefix;
    
    /**
     * The constructor initializes the session prefix and opens the session.
     *
     * @param bool $prefix A key to use for the session data
     */
    public function __construct($prefix = null)
    {
        if (!$prefix) {
            $prefix = basename(getcwd());
        }

        $this->prefix = $prefix;
        
        $this->open();
    }
    
    /**
     * Starts a session if none is started.
     * 
     * @return bool True if the session is started
     */
    public function open()
    {
        $this->session_id = session_id();
        
        if ("" === $this->session_id) {
            session_start();
            $this->session_id = session_id();
        }
        
        if (!array_key_exists($this->prefix(), $_SESSION)) {
            $_SESSION[$this->prefix()]= array();
        }

        return !empty($this->session_id);
    }

    /**
     * Get or set the session prefix key
     *
     * @param string $prefix Session prefix key
     * @return string
     */
    public function prefix($prefix = null)
    {
        if ($prefix) {
            $this->prefix = $prefix;
        }

        return $this->prefix;
    }
    
    /**
     * Checks if there is a session open.
     * 
     * @return boolean Returns true if there is a session, false otherwise
     */
    public function is_open()
    {
        return (session_id() !== "") && $this->session_id;
    }
    
    /**
     * Closes the current session if there is one.
     * 
     * @return bool True if the session was open beforehand, false otherwise
     */
    public function close()
    {
        if ($this->is_open()) {
            session_destroy();
            $this->session_id = false;
            return true;
        }

        return false;
    }
    
    /**
     * Stores a value in the session array.
     * 
     * @param $key string The key for the data
     * @param $value mixed The value of the data
     * @return mixed The value written
     */
    public function write($key, $value)
    {
        if (!$this->is_open()) {
            $this->open();
        }
        
        $_SESSION[$this->prefix()][$key] = $value;

        return $value;
    }
    
    /**
     * Retrieves a value from the session array, and returns another value if
     * the key is not present.
     * 
     * @param $key string The key for the data
     * @param $alternate_value string Optional value to return if key is unset
     * @return mixed The value of the data
     */
    public function read($key, $alternate_value = null)
    {
        if (!$this->is_open()) {
            $this->open();
        }
        
        if (isset($_SESSION[$this->prefix()][$key])) {
            return $_SESSION[$this->prefix()][$key];
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
     */
    public function exists($key)
    {
        if (!$this->is_open()) {
            return null;
        }
        
        return array_key_exists($key, $_SESSION[$this->prefix()]);
    }

    /**
     * Removes a value from the session array.
     * 
     * @param $key string The key to delete
     * @return void
     */
    public function delete($key)
    {
        if (!$this->is_open()) {
            $this->open();
        }
        
        unset($_SESSION[$this->prefix()][$key]);
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
