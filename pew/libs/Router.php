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

        list($url, $dest) = $route;

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
            if ($this->match_route($route, $segments));
        }

        var_dump($this->routes[$request_method]);
        throw new \Exception("Not yet implemented");
    }

    private function match_route($route, $segments)
    {
        $pattern = preg_replace('~:([^/]+)~', '(?P<$1>[^\/]+)', $route[0]);
        var_dump("matching [$segments] to [{$pattern}]");

        if (preg_match("~$pattern~", $segments, $matches)) {
            var_dump($matches);
        }
    }
}
