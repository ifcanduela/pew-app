<?php

/**
 * @package sys
 */

/**
 * The PewRequest class handles data coming from the server.
 * 
 * This class is meant to simplify handling of requests inside the App class and
 * provide a reusable repository of general information about the current task
 * of the application.
 *
 * @package sys
 * @version 0.2 14-may-2012
 * @author ifernandez <ifcanduela@gmail.com>
 */
class PewRequest
{
    /**
     * Normal output type 
     */
    const OUTPUT_TYPE_HTML = 'html';
    
    /**
     * Output type for ':' modifier 
     */
    const OUTPUT_TYPE_JSON = 'json';
    
    /**
     * Output type for '@' modifier 
     */
    const OUTPUT_TYPE_XML = 'xml';

    /**
     * Output type prefixes
     */
    protected static $_output_type_prefixes = array(
            self::OUTPUT_TYPE_HTML => '',
            self::OUTPUT_TYPE_JSON => ':',
            self::OUTPUT_TYPE_XML => '@'
        );
    
    /**
     * Default controller to use if no first segment provided.
     * 
     * @var string
     * @access protected
     */
    protected $_default_controller;
    
    /**
     * Default action to use if no second segment provided.
     * 
     * @var string
     * @access protected
     */
    protected $_default_action;
    
    /**
     * The user-created routing rules.
     * 
     * @var array
     */
    protected static $_routes = array();
    
    /**
     * Stores the type of HTTP request (GET, POST, PUT, DELETE, HEAD, OPTIONS
     * TRACE or CONNECT.
     * 
     * @var string
     */
    public $request_method = 'GET';
    
    /**
     * The intended format of the view output
     *
     * @var string
     * @ccess public
    */
    public $output_type = self::OUTPUT_TYPE_HTML;
    
    /**
     * The string with the segments.
     * 
     * @var string
     */
    public $uri = '';
    
    /**
     * Controller in the current request.
     * 
     * @var string
     */
    public $controller = '';
    
    /**
     * Action in the current request.
     * 
     * @var string
     */
    public $action = '';

    /**
     * View for the current request.
     * 
     * @var string
     */
    public $view = '';
    
    /**
     * First numeric value in the URL.
     * 
     * @var integer
     */
    public $id = null;
    
    /**
     * Full HTTP query string.
     * 
     * @var array
     */
    public $query_string = array();
    
    /**
     * Arguments to be passed to controller actions.
     * 
     * Any segment beyond the action is passed to the action. The keys of 
     * key:value pairs are stripped.
     * 
     * @var array
     */
    public $values = array();
    
    /**
     * The key:value pairs of the URL.
     * 
     * @var array
     */
    public $named = array();
    
    /**
     * The HTTP GET variables and their values
     * 
     * @var array
     */
    public $get = array();
    
    /**
     * The HTTP POST variables and their values
     * 
     * @var array
     */
    public $post = array();
    
    /**
     * Alias for the PHP $_FILES array
     * 
     * @var array
     */
    public $files = array();
    
    /**
     * Creates and initialises an HTTP request wrapper object.
     *
     * @param string $query_string An optional query string
     */
    function __construct($query_string = null)
    {
        # The query string
        if (isset($query_string)) {
            $this->query_string = $query_string;
        } elseif (isset($_SERVER['QUERY_STRING'])) {
            $this->query_string = $_SERVER['QUERY_STRING'];
        } else {
            $this->query_string = '';
        }
        
        # GET or POST, mostly
        $this->request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        
        # GET values in URL
        if (isset($_GET) && count($_GET) > 0) {
            foreach ($_GET as $k => $v) {
                $this->get[$k] = pew_clean_string($v);
            }
        } else {
            $this->get = false;
        }
        
        # POST values in request
        if (isset($_POST) && count($_POST) > 0) {
            $this->request_method = 'POST';
            foreach ($_POST as $k => $v) {
                # Used to call pew_clean_string, but decided to drop it
                $this->post[$k] = $v;
            }
        } else {
            $this->post = false;
        }
        
        # FILES
        if (isset($_FILES) && count($_FILES) > 0) {
            $this->request_method = 'POST';
            foreach ($_FILES as $file) {
                $this->files[] = $file;
            }
        } else {
            $this->files = false;
        }
    }
    
    /**
     * Returns the request object to a default state.
     * 
     * @param bool $reset_routes If true, configured routes will be deleted
     */
    public function reset($reset_routes = false)
    {
        $this->id = null;
        $this->query_string = '';
        $this->controller = $this->action = '';
        $this->values = $this->named = array();
        $this->get = $this->post = $this->files = array();
        
        if ($reset_routes === true) {
            self::$_routes = array();
        }
    }
    
    /**
     * Initialises the PewRequest.
     * 
     * @param string $uri_string The part the URL after the base locatio of the website
     * @throws Exception When no controller can be found
     */
    public function parse($uri_string = null)
    {
        $params = pew_clean_string($uri_string);
		
        $this->uri = $params;
                
        # Separate the query string into chunks
        $tags = explode('/', trim($params, '/'));
        
        # Filter the tags to remove URL-encoded characters and whitespace
        array_walk($tags, function(&$item) { 
            $item = pew_clean_string($item); 
        });
        
        # The first tag is the controller
        if (isset($tags[0]) && !empty($tags[0])) {
            $this->controller = str_replace('-', '_', $tags[0]);
        } else {
            if ($this->_default_controller) {
                $this->controller = $this->_default_controller;
            } else {
                # No controller name could be found
                throw new InvalidArgumentException("No controller segment found in [$params]");
            }
        }
        
        # The second tag is the action
        if (isset($tags[1])) {
            $this->action = str_replace('-', '_', $tags[1]);
        } else {
            if ($this->_default_action) {
                $this->action = $this->_default_action;
            } else {
                throw new InvalidArgumentException("No action segment found in [$params]");
            }
        }
        
        # The rest of the tags are additional parameters
        for ($i = 2; $i < count($tags); $i++) {
            # the first numeric tag is considered a primary key
            if (!isset($this->id) && is_numeric($tags[$i])) {
                $this->id = (int) $tags[$i];
            }
            
            if (strpos($tags[$i], ':')) {
                # Named tags (key:value) are special
                list($key, $val) = explode(':', $tags[$i]);
                
                $this->named[$key] = $val;
                $this->values[] = $val;
            } else {
                # The rest of the tags are just added to the array
                $this->values[] = $tags[$i];
            }
        }
        
        # Setup the output type
        switch ($this->action{0}) {
            case '_':
                # Actions prefixed with an underscore are private
                throw new InvalidArgumentException("Action is forbidden: {$this->action}");
                break;
            case self::$_output_type_prefixes[self::OUTPUT_TYPE_XML]:
                # actions prefixed with an at-sign are XML
                $this->output_type = self::OUTPUT_TYPE_XML;
                break;
            case self::$_output_type_prefixes[self::OUTPUT_TYPE_JSON]:
                # actions prefixed with a colon are JSON
                $this->output_type = self::OUTPUT_TYPE_JSON;
                break;
            default:
                # normal actions are HTML
                $this->output_type = self::OUTPUT_TYPE_HTML;
        }
        
        # Remove the extra characters and setup the view and action parameters
        $this->view = $this->action = ltrim($this->action, ':@ _');
    }
    
    /**
     * Collects all information related to the current request.
     * 
     * The returned array has the following indexes:
     *   - id:          first numeric segment in the URI string
     *   - controller:  controller slug
     *   - action:      action slug
     *   - view:        same as the action slug
     *   - passed       all segments after the first two
     *   - named        key/value segments
     *   - form         posted values
     *   - get          server query_string
     *   - files        uploaded files
     * @return array
     */
    public function segments()
    {
        $id = $this->id;
        $controller = $this->controller;
        $action = $this->action;
        $view = $this->view;
        $named = $this->named;
        $form = $this->post;
        $get = $this->query_string;
        $files = $this->files;
        $passed = $this->values;
        
        return compact('controller', 'action', 'view', 'id', 'named', 'form', 'get', 'files', 'passed');
    }

    /**
     * Retrieves the segment in the specified position.
     * 
     * Segments are numbered starting with 1, after controller and action.
     * 
     * @return string The value of the segment, or null if undefined
     */
    public function segment($position)
    {
        if (isset($this->values[$position - 1])) {
            return $this->values[$position - 1];
        }

        return null;
    }
    
    /**
     * Configures default controller and default action.
     * 
     * @param type $default_controller
     * @param type $default_action 
     */
    public function set_default($default_controller, $default_action)
    {
        $this->_default_controller = $default_controller;
        $this->_default_action = $default_action;
    }
    
    /**
     * Links a URL pattern and a destination.
     * 
     * A call with this form:
     *     ::add_route('/^controller/fake_action(\/?)', 'controller/real_action$1')
     * 
     * Will substitute these requests (among others):
     *     http://example.com/controller/fake_action
     *     http://example.com/controller/fake_action/
     *     http://example.com/controller/fake_action/param1
     * 
     * With these:
     *     http://example.com/controller/real_action
     *     http://example.com/controller/real_action/
     *     http://example.com/controller/real_action/param1
     * 
     * Be careful when using $ in the substitution string! Better use single quotes.
     * 
     * @param string $pattern A regular expression to match a URL
     * @param string $route The destination
     * @return int Position in the routes list
     */
    static function add_route($pattern, $route)
    {
        array_unshift(self::$_routes, array('pattern' => $pattern, 'route' => $route));
        return count(self::$_routes);
    }
    
    /**
     * Translates a URI string into a configured route, if there is a match.
     * 
     * @return string The remapped URL string
     */
    function remap($url)
    {
        if (count(self::$_routes) > 0) {
            foreach (self::$_routes as $v) {
                if (preg_match($v['pattern'], $url)) {
                    return preg_replace($v['pattern'], $v['route'], $url);
                }
            }
        }
        
        return $url;
    }
}
