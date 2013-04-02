<?php

namespace pew\libs;

class Router
{
    const HTML = 'html';
    const XML  = 'xml';
    const JSON = 'json';

    private $default_controller = '';
    private $default_action     = '';

    private $controller;
    private $action;
    private $parameters = [];

    private $routes = [];

    private $uri;

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
            throw new \Exception("Malformed route found.");
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

    /**
     * Set or get the default controller.
     *
     * @param string $controller
     * @return string The controller name
     */
    public function default_controller($controller = null)
    {
        if (!is_null($controller)) {
            if (is_string($controller)) {
                $this->default_controller = $controller;
            } else {
                throw new \InvalidArgumentException(__CLASS__ . '::' . __FUNCTION__ . '  expects a string');
            }
        }

        return $this->default_controller;
    }

    /**
     * Set or get the default action.
     *
     * @param  string $action
     * @return string The action name
     */
    public function default_action($action = null)
    {
        if (!is_null($action)) {
            if (is_string($action)) {
                $this->default_action = $action;
            } else {
                throw new \InvalidArgumentException(__CLASS__ . '::' . __FUNCTION__ . '  expects a string');
            }
        }

        return $this->default_action;
    }


    /**
     * Get the controller.
     * 
     * @return string The controller name
     */
    public function controller()
    {
        return $this->controller ? : $this->default_controller;
    }

    /**
     * Get the action.
     * 
     * @return string The action name
     */
    public function action()
    {
        if ($this->action) {
            if (!ctype_alpha($this->action{0})) {
                return substr($this->action, 1);
            }

            return $this->action;
        }

        return $this->default_action;
    }

    /**
     * Get the parameters array.
     * 
     * @return array Parameters
     */
    public function parameters($n = null)
    {
        if (is_numeric($n)) {
            if (array_key_exists($n, $this->parameters)) {
                return $this->parameters[$n];
            } else {
                throw new \RuntimeException("Route parameter not found: $n");
            }
        } else {
            return $this->parameters;
        }
    }

    public function response_type()
    {
        if (!ctype_alpha($this->action{0})) {
            switch ($this->action{0}) {
                case ':':
                    return self::JSON;
                case '@':
                    return self::XML;
            }
        }

        return self::HTML;
    }

    /**
     * Get the current URI.
     * 
     * @return string The URI
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Extracts controller, action and parameters from a URI.
     * 
     * @param string $uri Uri from the browser
     * @param string $request_method HTTP request verb
     * @return Router The Router object
     */
    public function route($uri, $request_method = 'GET')
    {
        $this->uri = $uri;

        $request_method = strtoupper($request_method);

        foreach ($this->routes[$request_method] as $route) {
            if ($matches = $this->match_route($route, $uri)) {
                $built_route = $this->build_route($uri, $route);
                break;
            }
        }

        if (!isSet($built_route)) {
            throw new \Exception("No route found");
        }

        $this->controller   = $built_route['controller'];
        $this->action       = $built_route['action'];
        $this->parameters   = $built_route['parameters'];

        return $this;
    }

    /**
     * Check if a URI matches a route.
     * 
     * @param array $route A route to match
     * @param string $segments URI to match
     * @return array|bool Matches or false
     */
    protected function match_route(&$route, $segments)
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
    protected function build_route($segments, $route = null)
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
        $parameters = isSet($segments[2]) ? array_slice($segments, 2) : array();

        return compact('controller', 'action', 'parameters');
    }
}
