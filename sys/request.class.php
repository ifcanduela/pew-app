<?php

/**
 * @package sys
 */

/**
 * The Request class handles data coming from the server.
 * 
 * This class is meant to simplify handling of requests inside the App class and
 * provide a reusable repository of general information about the current task
 * of the application.
 *
 * @package sys
 * @author ifernandez <ifcanduela@gmail.com>
 */
class Request
{
    private $segment_separator = '/';
    private $get = array();
    private $post = array();
    private $segments = array();
    private $routes = array();
    
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
    }

    public function build($segment_string, $segment_separator = null)
    {
        if (!$segment_separator) {
            $segment_separator = $this->segment_separator;
        }
        
        $this->segments = explode($segment_separator, $segment_string);
    }
    
    protected function fetch($collection, $key, $fallback)
    {
        if (!$key) {
            return $collection;
        } elseif (isset($collection[$key])) {
        
            return $collection[$key];
        }
        
        return $fallback;
    }
    
    public function get($key = null, $fallback = null)
    {
        return $this->fetch($this->get, $key, $fallback);
    }
    
    public function post($key = null, $fallback = null)
    {
        return $this->fetch($this->post, $key, $fallback);
    }

    public function segment($index, $fallback = null)
    {
        return $this->fetch($this->segments, $key, $fallback);
    }

    public function segments()
    {
        return $this->segments;
    }
    
    public function add_route($from, $to, $method = 'get')
    {
        $route = array(
                'from' => ltrim($from, '/'),
                'to' => ltrim($to, '/'),
                'method' => $method,
                'regexp' => '~' . preg_replace('/\*/', '([A-Za-z0-9-_]*)', $from) . '~',
            );
        $this->routes[] = $route;
    }

    public function route($path)
    {
        foreach ($this->routes as $r) {
            preg_match($r['regexp'], $path, $matches);

            if (!empty($matches)) {
                $destination = $r['to'];
                foreach ($matches as $key => $value) {
                    $destination = str_replace(":$key", $matches[$key], $destination);
                }
                $remap = clone $this;
                $remap->build($destination);

                return $remap;
            }
        }

        return null;
    }
}
