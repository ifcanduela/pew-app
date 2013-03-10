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
        # code...
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

    public function route($segments)
    {
        throw new \Exception("Not yet implemented");
    }
}
