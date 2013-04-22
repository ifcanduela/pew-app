<?php

namespace pew\libs;

/**
 * Simple registry class to store key/value pairs.
 *
 * Can be instantiated with the new keyword or as a singleton through
 * the instance() method.
 *
 * @package pew/libs
 * @author ifcanduela <ifcanduela@gmail.com>
 */
class Registry implements \Countable, \ArrayAccess
{
    /**
     * @var Registry Singleton instance of the registry
     */
    private static $instance = null;

    /** 
     * @var array Key/value list
     */
    private $items = array();

    /**
     * Constructor.
     *
     * Set to public to allow instantiation of multiple registries.
     */
    public function __construct()
    {
        
    }

    /**
     * Retrieves a singleton instance of the class.
     * 
     * @return Registry Singleton registry
     * @static
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Import a set of keys and values into the current registry.
     * 
     * @param array $data Associative array with keys and values to import.
     * @return int Number of new values
     */
    public function import(array $data)
    {
        $prev_count = count($this->items);

        foreach ($data as $key => $value) {
            $this->items[$key] = $value;
        }

        return count($this->items) - $prev_count;
    }

    /**
     * Checks whether the registry is empty.
     * 
     * @return boolean True if the registry is empty, false otherwise
     */
    public function is_empty()
    {
        return count($this->items) === 0;
    }

    /**
     * Counts the number of stored items.
     *
     * Countable implementation.
     * 
     * @return int Number of stored items
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Checks if an offset exists.
     *
     * ArrayAccess implementation.
     * 
     * @return boolean True if the offset exists, false otherwise
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * Gets the value at an offset.
     *
     * ArrayAccess implementation.
     * 
     * @return mixed The value at the offset.
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * Set the value for an offset.
     *
     * ArrayAccess implementation.
     * 
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        return $this->__set($offset, $value);
    }

    /**
     * Removes an offset.
     *
     * ArrayAccess implementation.
     * 
     * @return boolean True if the offset is removed, false otherwise
     */
    public function offsetUnset($offset)
    {
        return $this->__unset($offset);
    }

    /**
     * Sets a value in the registry.
     * 
     * @param mixed $key Key for the value
     * @param mixed Value to store
     */
    public function __set($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * Gets a stored value from the registry.
     * 
     * @param mixed $key Key for the value
     * @return mixed Value stored or NULL
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        return null;
    }

    /**
     * Checks if a key is in use.
     * 
     * @param mixed $key Key to check
     * @return boolean True if the key has been set, false otherwise.
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Removes a stored value from the registry.
     * 
     * @param mixed $key Key to delete
     * @return boolean True on success, true on failure
     */
    public function __unset($key)
    {
        if (array_key_exists($key, $this->items)) {
            unset($this->items[$key]);
            return true;
        }

        return true;
    }
}
