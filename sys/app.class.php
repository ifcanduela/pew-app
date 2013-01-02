<?php

/**
 * @package sys
 */

/**
 * The App class is a simple interface between the front controller and the
 * rest of the controllers.
 * 
 * @version 0.20 13-mar-2012
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
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
    public function run($url_segment = 'url')
    {
        # get the current request info
        $request = Pew::request();
        $segments = $request->get($pathKeyName);
        $request->route($segments);

        # instantiate a controller and a view
        $controller = Pew::controller($request->controller);
        $view = Pew::view();

        # check controller instantiation
        if (!is_object($controller)) {
            if (file_exists(Pew::config()->views_folder . $request->controller . DS . $request->action . Pew::config()->view_ext)) {
                # if the controller does not exist, but the view does, use Pages
                $controller = Pew::controller('pages');
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
        # @deprecated
        if (Pew::config()->use_auth && $controller->auth->require() ){
            # check if the user is authenticated
            if (!Pew::auth()->gate()) {
                # save the current request for later
                Pew::session()->referrer = $request->uri;
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
            $output = $view->render($view_data);
            
            # render the layout
            $layout = Pew::view('layout');
            $layout->template($view->layout());
            $layout->render(array('title' => $view->title, 'output' => $output));
        }
    }
}
