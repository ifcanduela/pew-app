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
    /**
     * Character that separates URL segments.
     * 
     * @var string
     * @access  private
     */
    private $segment_separator = '/';

    /**
     * GET key/value pairs for the request.
     * 
     * @var array
     * @access  private
     */
    private $get = array();

    /**
     * POST key/value pairs for the request.
     * 
     * @var array
     * @access  private
     */
    private $post = array();

    /**
     * List of segments.
     * 
     * @var array
     * @access  private
     */
    private $segments = array();

    /**
     * List of configured routes.
     * 
     * @var array
     * @access  private
     */
    private $routes = array();
    
    /**
     * 
     */
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
    }

    /**
     * Builds a request based on the segments.
     * 
     * @param string $segment_string Segments in string format
     * @param string $segment_separator Options segment separator character
     * @return Request The request object
     */
    public function build($segment_string, $segment_separator = null)
    {
        if (!$segment_separator) {
            $segment_separator = $this->segment_separator;
        }
        
        $this->segments = array_filter(explode($segment_separator, $segment_string));

        /// @todo Gather additional request info, like referrer and other stuff

        return $this;
    }
    
    protected function fetch($collection, $key, $fallback)
    {
        if (is_null($key)) {
            return $collection;
        } elseif (array_key_exists($key, $collection)) {
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
        return $this->fetch($this->segments, $index, $fallback);
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

    /**
     * Transforms the request according to the configured routes.
     * 
     * @param string $path Path to check
     * @return Request A new Request object if there's a match, null otherwise
     */
    public function route($path)
    {
        $path = $this->segment_separator . trim($path, $this->segment_separator);

        foreach ($this->routes as $r) {
            preg_match($r['regexp'], $path, $matches);

            if (!empty($matches)) {
                $destination = $r['to'];
                $additional_segments = str_replace($matches[0], '', $path);

                foreach ($matches as $key => $value) {
                    $destination = str_replace(":$key", $matches[$key], $destination);
                }
                $remap = clone $this;
                $remap->build($destination . $additional_segments);

                return $remap;
            }
        }

        return null;
    }
}
