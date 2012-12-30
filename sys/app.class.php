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
     * The $auth variable is initialized if USEAUTH is true in config.php.
     *
     * @access public
     * @var Auth
     */
    public $auth = null;
	
	/**
     * The URL segments for the current request.
     *
     * @var string
     * @access public
     */
    public $url = '';
    
    /**
     * HTTP request-related data.
     *
     * @var array
     * @access public
     */
    public $segments = array();
    
    /**
     * The main controller.
     *
     * @var Controller
     * @access public
     */
    public $controller = null;

    /**
     * The view object.
     *
     * @var View
     * @access public
     */
    public $view = null;
    
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
    public function run($uri_string = '')
    {
        # get the URI segments, if they are available
        if (!$uri_string && isset($_GET['url'])) {
            $uri_string = $_GET['url'];
        }
        
        # get the PewRequest object
        $request = Pew::request($uri_string);
        
        # controller instantiation
        $controller_class = file_name_to_class_name($request->controller);
        $this->controller = Pew::controller($controller_class, $request);
        $this->view = Pew::view();
        
        # check controller instantiation
        if (!is_object($this->controller)) {
            if (file_exists(VIEWS . $request->controller . DS . $request->action . VIEW_EXT)) {
                # if the controller does not exist, but the view does, use Pages
                $this->controller = Pew::get('Pages', $request);
                $this->controller->view_folder = $request->controller;
            } else {
                # display an error page if the controller could not be instanced
                new PewError(CONTROLLER_MISSING);
            }
        }
        
        # call the before_action method if it's defined
        if (method_exists($this->controller, 'before_action')) {
            $this->controller->before_action();
        }
        
        # call the action method and let the controller decide what to do
        $this->controller->_action();
        
        # check if the controller action requires authentication
        # @deprecated
        if (USEAUTH && $this->controller->require_auth) {
            # check if the user is authenticated
            if (!$this->auth->gate()) {
                # save the current request for later
                $this->session->referrer = $request->uri;
                # display the login page
                redirect('users/login');
            }
        }

        # call the after_action method if it's defined
        if (method_exists($this->controller, 'after_action')) {
            $this->controller->after_action();
        }
        
        # render the view, if not prevented
        if ($this->controller->render) {
            $this->view->layout = $this->controller->layout;
            $this->view->title = $this->controller->title;
            
            $this->view->render($this->controller->view, $this->controller->data);
            //$this->controller->_view();
        }
    }
}
