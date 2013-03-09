<?php

namespace pew\libs;

/**
 * The Request class handles data coming from the server.
 * 
 * This class is meant to simplify handling of requests inside the App class and
 * provide a reusable repository of general information about the current task
 * of the application.
 *
 * @package pew\libs
 * @author ifernandez <ifcanduela@gmail.com>
 */
class Request
{
    private $method;
    private $headers;
    private $scheme;
    private $host;
    private $port;
    private $path;
    private $script;
    
    private $segments;

    private $get;
    private $post;
    private $files;
    private $cookie;

    private $local;

    public function __construct()
    {
        if (PHP_SAPI === 'cli') {
            $this->method = 'CLI';
        } else {
            $this->method   = isSet($_POST['_method']) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];
            $this->headers  = getAllHeaders();
            $this->scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $this->host     = $_SERVER['SERVER_NAME'];
            $this->port     = $_SERVER['SERVER_PORT'];
            $this->path     = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            $this->script   = basename($_SERVER['SCRIPT_NAME']);

            if (isSet($_SERVER['PATH_INFO'])) {
                $this->segments = $_SERVER['PATH_INFO'];
            } else {
                $request_script_name = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
                $script_relative = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $request_script_name);
                $segments = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $script_relative);
                $this->segments = '/' . trim($segments, '/');   
            }
            

            $this->get      = $_GET;
            $this->post     = $_POST;
            $this->files    = $_FILES;
            $this->cookie   = $_COOKIE;

            $this->local = in_array($_SERVER['REMOTE_ADDR'], ['localhost', '127.0.0.1', '::1']);

            // @todo: remove this call
            $this->dump_vars();
        }
    }

    // @todo: remove this method
    private function dump_vars()
    {
        $vars = ['_SERVER' => $_SERVER, '_GET' => $_GET, '_POST' => $_POST, '_FILES' => $_FILES, '_COOKIE' => $_COOKIE];
        var_dump($vars);
    }

    private function fetch(array $array, $key, $default)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        return $default;
    }

    public function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->get;
        } else {
            return $this->fetch($this->get, $key, $default);
        }
    }

    public function post($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->post;
        } else {
            return $this->fetch($this->get, $key, $default);
        }
    }

    /**
     * Get the value of a specified property.
     * 
     * @param string $property Property name
     * @param array $args Function arguments
     * @return mixed Property value
     */
    public function __call($property, array $args = [])
    {
        if (isSet($this->$property)) {
            return $this->$property;
        }

        throw new \RuntimeException("Unknown method $key");
    }
}
