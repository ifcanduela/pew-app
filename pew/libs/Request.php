<?php

namespace pew\libs;

/**
 * The Request class handles data coming from the server.
 * 
 * This class is meant to simplify handling of requests inside the App class and
 * provide a reusable repository of general information about the current task
 * of the application.
 *
 * @package pew/libs
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
            $this->scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $this->host     = $_SERVER['SERVER_NAME'];
            $this->port     = $_SERVER['SERVER_PORT'];
            $this->path     = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            $this->script   = basename($_SERVER['SCRIPT_NAME']);

            if (function_exists('getAllHeaders')) {
                $this->headers  = getAllHeaders();
            }
            
            if (isSet($_SERVER['PATH_INFO'])) {
                $this->segments = $_SERVER['PATH_INFO'];
            } else {
                if (false !== $question_mark_position = strpos($_SERVER['REQUEST_URI'], '?')) {
                    $request_script_name = substr($_SERVER['REQUEST_URI'], 0, $question_mark_position);
                } else {
                    $request_script_name = $_SERVER['REQUEST_URI'];
                }
                $script_relative = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $request_script_name);
                $segments = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $script_relative);
                $this->segments = '/' . trim($segments, '/');    
            }
            
            $this->get      = $_GET;
            $this->post     = $_POST;
            $this->files    = $_FILES;
            $this->cookie   = $_COOKIE;

            $this->local = in_array($_SERVER['REMOTE_ADDR'], ['localhost', '127.0.0.1', '::1']);
        }
    }

    protected function fetch(array $array, $key, $default)
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
            return $this->fetch($this->post, $key, $default);
        }
    }

    public function files($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->files;
        } else {
            return $this->fetch($this->files, $key, $default);
        }
    }

    /**
     * Gets the segment string or one of the segments.
     * 
     * @param  int $segment
     * @return string|null
     */
    public function segments($segment = null)
    {
        static $segments;
        if (is_numeric($segment)) {
            if (!$segments) {
                $segments = explode('/', trim($this->segments, '/'));
            }
            return array_key_exists($segment, $segments) ? $segments[$segment] : null;
        } else {
            return $this->segments;
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

        throw new \RuntimeException("Unknown method $property");
    }
}
