<?php

namespace pew\libs;

class Router
{
    private $default_controller = '';
    private $default_action     = '';

    private $routes = [];

    public function __construct(array $routes = [])
    {
        foreach ($routes as $key => $route) {
            $this->add_route($route);
        }
    }

    /**
     * Adds a route to the routing arrays.
     * 
     * @param array $route Array with methods, origin and destination
     */
    public function add_route(array $route)
    {
        if (count($route) < 2) {
            return;
        }

        $route[0] = '/'. trim($route[0], '/');
        $route[1] = '/'. trim($route[1], '/');

        if (isSet($route[2])) {
            $methods = preg_split('/\W+/', strtoupper($route[2]));
        } else {
            $methods = ['GET'];
        }

        foreach ($methods as $method) {
            if (!isSet($this->routes[$method])) {
                $this->routes[$method] = [];
            }

            $this->routes[$method][] = $route;
        }
    }

    public function default_controller($controller)
    {
        if (!is_string($controller)) {
            throw new \InvalidArgumentException(__CLASS__ . '::' . __FUNCTION__ . '  expects a string');
        }

        $this->default_controller = $controller;
    }

    public function default_action($action)
    {
        if (!is_string($action)) {
            throw new \InvalidArgumentException(__CLASS__ . '::' . __FUNCTION__ . '  expects a string');
        }

        $this->default_action = $action;
    }

    public function route($segments, $request_method = 'GET')
    {
        $request_method = strtoupper($request_method);

        foreach ($this->routes[$request_method] as $route) {
            if ($matches = $this->match_route($route, $segments)) {
                return $this->build_route($segments, $route);
            }
        }

        return $this->build_route($segments);
    }

    /**
     * Check if a URI matches a route.
     * 
     * @param array $route A route to match
     * @param string $segments URI to match
     * @return array|bool Matches or false
     */
    private function match_route(&$route, $segments)
    {
        $pattern = preg_replace('~:([^/]+)~', '(?P<$1>[^\/]+)', $route[0]);

        if (preg_match("~$pattern~", $segments, $matches)) {
            $route['pattern'] = "~$pattern~";
            foreach ($matches as $k => $v) {
                if (is_numeric($k)) {
                    unset($matches[$k]);
                }
            }
            $route['matches'] = $matches;

            return $route;
        }

        return false;
    }

    /**
     * Builds a path with controller/action/parameters elements
     * 
     * @param string $segments URI segments
     * @param array $route Transformation route
     * @param array $matches Transformation values
     * @return array The transformed URI elements
     */
    public function build_route($segments, $route = null)
    {
        $transformed = $route[0];
        $destination = $route[1];

        foreach ($route['matches'] as $placeholder => $match) {
            $destination = str_replace(":$placeholder", $match, $destination);
            $transformed = str_replace(":$placeholder", $match, $transformed);
        }

        $segments = str_replace($transformed, $destination, $segments);
        $segments = array_values(array_filter(explode('/', $segments)));

        $controller = isSet($segments[0]) ? $segments[0] : $this->default_controller;
        $action     = isSet($segments[1]) ? $segments[1] : $this->default_action;
        $arguments  = isSet($segments[2]) ? array_slice($segments, 2) : array();

        return compact('controller', 'action', 'arguments');
    }
}
