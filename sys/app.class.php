<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

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
        # initialise Authentication if required
        if (USEAUTH) {
            $this->auth = Pew::Get('Auth');
        }
        
        # starts a session if required
        if (USESESSION) {
            $this->session = Pew::Get('Session');
            $this->session->open();
        }
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
        $request = Pew::GetRequest($uri_string);
        
        # controller instantiation
        $controller_class = file_name_to_class_name($request->controller);
        $this->controller = Pew::GetController($controller_class, $request);
        $this->view = Pew::Get('View', $request);
        
        # check controller instantiation
        if (!is_object($this->controller)) {
            if (file_exists(VIEWS . $request->controller . DS . $request->action . VIEW_EXT)) {
                # if the controller does not exist, but the view does, use Pages
                $this->controller = Pew::GetController('Pages', $request);
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
                $this->session->referrer = $this->segments['uri'];
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
    
    /**
     * Builds an array containing various URL elements, such as controller,
     * action, form data, id, etc.
     *
     * This method will create an array, its elements being the parameters
     * received via HTTP in various forms:
     *
     *    - uri
     *    - response_format
     *    - controller
     *    - action
     *    - id
     *    - form
     *    - tags[]
     *    - named[]
     *    - numbered[]
     *    - passed[]
     * 
     * @param string $params A slash-separated string of url parameters
     * @return array A normalized list of url and application parameters
     * @access protected
     * @todo: change $params to $uri and $return to $segments
     * @todo: add support for $_FILES
     */
	/*
    public function get_segments($params)
    {
	$params = pew_clean_string($params);
		
        # $return will hold the segments and the original URI
        $return = array('uri' => $params);
        
        # GET or POST, mostly
        $return['request_method'] = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        
        # arrays for the different categories
        $named = array();
        $numbered = array();
        
        # separate the query string into chunk tags
        $tags = explode('/', trim($params, '/'));
        
        # filter the tags to remove URL-encoded characters and whitespace
        array_walk($tags, function(&$item) { 
            $item = pew_clean_string($item); 
        });
        
        # the first tag is the controller
        if (isset($tags[0]) && !empty($tags[0])) {
            $return['controller'] = str_replace('-', '_', $tags[0]);
        } else {
            # no controller was specified
            $return['controller'] = DEFAULT_CONTROLLER;
        }
        
        # the second tag is the action
        if (isset($tags[1])) {
            $return['action'] = str_replace('-', '_', $tags[1]);
        } else {
            # no action was specified
            if ($return['controller'] === DEFAULT_CONTROLLER) {
                # if the controller is the default, use the default action
                $return['action'] = DEFAULT_ACTION;
            } else {
                # otherwise, the action is index
                $return['action'] = 'index';
            }
        }
        
        # the rest of the tags are additional parameters
        for ($i = 2; $i < count($tags); $i++) {
            # the first numeric tag is considered a primary key
            if (!isset($return['id']) && is_numeric($tags[$i])) {
                $return['id'] = (int) $tags[$i];
            }
            
            if (strpos($tags[$i], ':')) {
                # named tags (key:value) are special
                list($key, $val) = explode(':', $tags[$i]);
                
                $return[] = $val;
                $named[$key] = $val;
            } else {
                # the rest of the tags are just added to the array
                $return[] = $tags[$i];
                $numbered[$i] = $tags[$i];
            }
        }
        
        # return the filtered segments
        $return['segments'] = $tags;
        
        # add any parameter beyond controller and action to the 'passed' array
        $return['passed'] = array_slice($tags, 2);
        
        # add the named key:value pairs
        $return['named'] = $named;
        
        # add the anonymous parameters
        $return['numbered'] = $numbered;
        
        # add the POST parameters if they exist
        if (isset($_POST) && !empty($_POST)) {
            $return['form'] = clean_array_data($_POST);
        }
        
        # and the GET parameters
        if (isset($_GET) && !empty($_GET)) {
            $return['query_string'] = array();
            foreach ($_GET as $k => $v) {
                $return['query_string'][$k] = pew_clean_string($v);
            }
        }
        
        if (USESESSION) {
            # update controller and action info in Session
            $this->session->controller = $return['controller'];
            $this->session->action = $return['action'];
        }
        
        # return the array
        return $return;
    }
	*/
}
