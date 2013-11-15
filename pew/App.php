<?php

namespace pew;

use pew\Pew;

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
     * Application entry point, manages controllers, actions and views.
     *
     * This function is responsible of creating an instance of the appropriate
     * Controller class and calling its action() method, which will handle
     * the controller call.
     */
    public function run()
    {
        $request = Pew::instance()->request();
        $router  = Pew::instance()->router();
        $view = Pew::instance()->view();

        $router->route($request->segments(), $request->method());
        
        # Instantiate the main view
        $view->template($router->controller() . '/' . $router->action());
        $view->layout(Pew::instance()->config()->default_layout);
        
        # instantiate the controller
        $controller = Pew::instance()->controller($router->controller());
        
        # check controller instantiation
        if (!is_object($controller)) {
            if ($view->exists()) {
                $view->title($router->action());
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

        # call the after_action method if it's defined
        if (method_exists($controller, 'after_action')) {
            $controller->after_action();
        }

        # render the view, if not prevented
        if ($view->render) {
            switch ($router->response_type()) {
                case 'json':
                    $page = json_encode($view_data);
                    header('Content-type: application/json');
                    break;
                case 'xml':
                    throw new \Exception('XML rendering is not yet implemented.');
                    break;
                default:
                    $page = $this->render($controller, $view, $view_data);
                    break;
            }

            echo $page;
        }
    }

    public function render($controller, $view, $view_data)
    {
        if (!$view->exists()) {
            $defaultView = clone $view;
            $defaultView->folder(Pew::instance()->config()->system_folder . '/views');

            if ($defaultView->exists()) {
                $output = $defaultView->render(null, $view_data);
            } else {
                throw new ViewTemplateNotFoundException("View file could not be found: {$view->folder()}/{$view->template()}{$view->template()}");
            }
        } else {
            $output = $view->render(null, $view_data);
        }

        if (method_exists($controller, 'before_render')) {
            $output = $controller->before_render($output);
        }

        $layout = clone $view;
        $layout->extension(Pew::instance()->config()->layout_ext);
        $layout->template($view->layout());

        if (!$layout->exists()) {
            $defaultLayout = clone($layout);
            $defaultLayout->folder(Pew::instance()->config()->system_folder . 'views');

            if (!$defaultLayout->exists()) {
                 throw new \Exception("Layout file could not be found: {$layout->folder()}/{$layout->template()}{$layout->extension()}");
            }

            $layout = $defaultLayout;
        }

        $output = $layout->render(null, ['title' => $view->title, 'output' => $output]);

        return $output;
    }
}
