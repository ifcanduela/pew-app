<?php

namespace pew;

/**
 * The App class is a simple interface between the front controller and the
 * rest of the controllers.
 * 
 * @package pew
 * @author ifcanduela <ifcanduela@gmail.com>
 */
class App
{
    /**
     * Initialization of components.
     *
     * @access public
     */
    function __construct()
    {

    }
    
    /**
     * Application entry point, manages controllers, actions and views.
     *
     * The dispatcher accepts a path string, which should be formed according
     * to the .htaccess rules in the root of the app directory structure. It
     * then retrieves a PewRequest object with current request info.
     *
     * This function is responsible of creating an instance of the appropriate
     * Controller class and calling its action() methods, which will handle
     * the data processing.
     *
     * When the action() method is done, the dispatcher checks if actions is
     * protected against non-authenticated access. If the check is passed, the
     * Controller::view() method is invoked.
     * 
     * @param string $params optional slash-separated string of url parameters
     * @access public
     */
    public function run()
    {
        $request = new libs\Request;
        $router  = Pew::router();
        var_dump($router);
        $route   = $router->route($request->segments());


        $controller_name = $route->segment(0) ? $route->segment(0) : Pew::config()->default_controller;

        # instantiate a controller and a view
        $controller = Pew::controller($controller_name, $request);
        $view = Pew::view();

        # check controller instantiation
        if (!is_object($controller)) {
            if (file_exists(Pew::config()->views_folder . $request->controller . DS . $request->action . Pew::config()->view_ext)) {
                # if the controller does not exist, but the view does, use Pages
                $controller = new controllers\Pages($request);
                $controller->view = $view;
                $view->templates_dir = $request->controller;
            } else {
                # display an error page if the controller could not be instanced
                new PewError(PewError::CONTROLLER_MISSING, $request);
            }
        }
        
        # assign the curreent view to the controller
        $controller->view = $view;

        # call the before_action method if it's defined
        if (method_exists($controller, 'before_action')) {
            $controller->before_action();
        }

        # call the action method and let the controller decide what to do
        $view_data = $controller->_action();
        
        # check if the controller action requires authentication
        if (isset($controller->auth) && $controller->auth->require()) {
            # check if the user is authenticated
            if (!$controller->auth->gate()) {
                # save the current request for later
                $controller->auth->referrer($request->uri);
                # display the login page
                redirect('users/login');
            }
        }

        # call the after_action method if it's defined
        if (method_exists($controller, 'after_action')) {
            $controller->after_action();
        }
        
        # render the view, if not prevented
        if ($view->render) {
            $output = $view->render($view_data, $request->segment(0) . '/' . $request->segment(1));
            
            # render the layout
            $layout = Pew::view('layout');
            $layout->template($view->layout());
            $layout->render(array('title' => $view->title, 'output' => $output));
        }
    }
}
