<?php

namespace pew;

/**
 * @package pew
 */

/**
 * Inplementation of the PSR-0 autoloader.
 * 
 * Simplified from Jonathan H. Wage's Gist at https://gist.github.com/jwage/221634
 */
class Autoloader
{
    const CLASS_FILE_EXTENSION = '.php';
    const NAMESPACE_SEPARATOR = '\\';

    protected $namespace;
    protected $base_path;

    /**
     * Constructs an Autoloader object.
     * 
     * @param string $namespace The namespace to use.
     * @param string $base The namespace to use.
     */
    public function __construct($namespace = null, $base_path = null)
    {
        $this->namespace = $namespace;
        $this->base_path = $base_path;
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     */
    public function register()
    {
        spl_autoload_register(array($this, 'load_class'));
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'load_class'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $class The name of the class to load.
     * @return void
     */
    public function load_class($class)
    {
        $namespaceName = $this->namespace . self::NAMESPACE_SEPARATOR;
        if (null === $this->namespace || $namespaceName === substr($class, 0, strlen($namespaceName))) {
            $fileName = '';
            $namespace = '';
            
            if (false !== ($lastNsPos = strripos($class, $this->_namespaceSeparator))) {
                $namespace = substr($class, 0, $lastNsPos);
                $class = substr($class, $lastNsPos + 1);
                $fileName = str_replace(self::NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }

            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $class) . $this->_fileExtension;

            require ($this->base_path !== null ? $this->base_path . DIRECTORY_SEPARATOR : '') . $fileName;
        }
    }
}

