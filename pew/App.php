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
    public function __construct()
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
        $request = Pew::request();
        $router  = Pew::router();

        $router->route($request->segments(), $request->method());

        $controller_name = $router->controller();
        # Instantiate the main view
        $view = Pew::view();

        $view->folder(Pew::config()->app_folder . DIRECTORY_SEPARATOR . 'views');
        $view->template($router->controller() . '/' . $router->action());
        $view->layout(Pew::config()->default_layout);

        # instantiate the controller
        $controller = Pew::controller($controller_name, $request);
        
        # check controller instantiation
        if (!is_object($controller)) {
            if ($view->exists()) {
                $controller = new controllers\Pages($request, $view);
                $skip_action = true;
            } else {
                # display an error page if the controller could not be instanced
                $controller = new controllers\Error($request);
                $controller->set_error(controllers\Error::CONTROLLER_MISSING);
            }
        }
        
        # call the before_action method if it's defined
        if (method_exists($controller, 'before_action')) {
            $controller->before_action();
        }

        # call the action method and let the controller decide what to do
        if (isSet($skip_action) && $skip_action) {
            $view_data = array();
        } else {
            $view_data = $controller->_action($router->action(), $router->parameters());
        }

        # check if the controller action requires authentication
        if (isset($controller->auth) && $controller->auth->require()) {
            # check if the user is authenticated
            if (!$controller->auth->gate()) {
                # save the current request for later
                $controller->auth->referrer($router->uri());
                # display the login page
                redirect('users/login');
            }
        }

        # call the after_action method if it's defined
        if (method_exists($controller, 'after_action')) {
            $controller->after_action();
        }

        $this->response($view, $view_data);
    }

    public function response($view, $data)
    {
        # render the view, if not prevented
        if ($view->render) {
            if (!$view->exists()) {
                $defaultView = clone $view;
                $defaultView->folder(Pew::config()->system_folder . 'views');

                if ($defaultView->exists()) {
                    $output = $defaultView->render($data);
                } else {
                    throw new \Exception("View file could not be found");
                }
            } else {
                $output = $view->render($data);
            }

            # render the layout
            $layout = clone $view;
            $layout->extension(Pew::config()->layout_ext);
            $layout->template($view->layout());
            echo $layout->render(['title' => $view->title, 'output' => $output]);
        }
    }
}
