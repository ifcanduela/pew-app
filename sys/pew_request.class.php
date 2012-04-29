<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'functions.php');

/**
 * The PewRequest class handles data coming from the server.
 * 
 * This class is meant to simplify handling of requests inside the App class and
 * provide a reusable repository of general information about the current task
 * of the application.
 *
 * @package sys
 * @version 0.1 10-mar-2012
 * @author ifernandez <ifcanduela@gmail.com>
 */
class PewRequest
{
    /**
     * Default controller to use if no first segment provided.
     * 
     * @var string
     */
    public $default_controller;
    
    /**
     * Default action to use if no second segment provided.
     * 
     * @var string
     */
    public $default_action;
    
    /**
     * The user-created routing rules.
     * 
     * @var array
     */
    static $routes = array();
    
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
    public $output_type = OUTPUT_TYPE_HTML;
    
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
    
    function __construct()
    {
        # The query string
        $this->query_string = $_SERVER['QUERY_STRING'];
        
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
            foreach ($_POST as $k => $v) {
                $this->post[$k] = pew_clean_string($v);
            }
        } else {
            $this->post = false;
        }
        
        # FILES
        if (isset($_FILES) && count($_FILES) > 0) {
            foreach ($_FILES as $file) {
                $this->files[] = $file;
            }
        } else {
            $this->files = false;
        }
    }
        
    public function reset($reset_routes = false)
    {
        $this->id = null;
        $this->query_string = '';
        $this->controller = $this->action = '';
        $this->values = $this->named = array();
        $this->get = $this->post = $this->files = array();
        
        if ($reset_routes === true) {
            self::$routes = array();
        }
    }
    
    function parse($params = null)
    {
        $params = pew_clean_string($params);
		
        # $return will hold the segments and the original URI
        $this->uri = $params;
                
        # separate the query string into chunk tags
        $tags = explode('/', trim($params, '/'));
        
        # filter the tags to remove URL-encoded characters and whitespace
        array_walk($tags, function(&$item) { 
            $item = pew_clean_string($item); 
        });
        
        # the first tag is the controller
        if (isset($tags[0]) && !empty($tags[0])) {
            $this->controller = str_replace('-', '_', $tags[0]);
        } else {
            if ($this->default_controller) {
                $this->controller = $this->default_controller;
            } else {
                throw new Exception("No controller segment found [$params]");
            }
        }
        
        # the second tag is the action
        if (isset($tags[1])) {
            $this->action = str_replace('-', '_', $tags[1]);
        } else {
            if ($this->default_action) {
                $this->action = $this->default_action;
            } else {
                throw new Exception("No action segment found [$params]");
            }
        }
        
        # the rest of the tags are additional parameters
        for ($i = 2; $i < count($tags); $i++) {
            # the first numeric tag is considered a primary key
            if (!isset($this->id) && is_numeric($tags[$i])) {
                $this->id = (int) $tags[$i];
            }
            
            if (strpos($tags[$i], ':')) {
                # named tags (key:value) are special
                list($key, $val) = explode(':', $tags[$i]);
                
                $this->named[$key] = $val;
                $this->values[] = $val;
            } else {
                # the rest of the tags are just added to the array
                $this->values[] = $tags[$i];
            }
        }
        
        # setup the output type
        switch ($this->action{0}) {
            case '_':
                # Actions prefixed with an underscore are private
                new PewError(ACTION_FORBIDDEN, $this, $this->action);
                break;
            case '@':
                # actions prefixed with an at-sign are XML
                $this->output_type = OUTPUT_TYPE_XML;
                break;
            case ':':
                # actions prefixed with a colon are JSON
                $this->output_type = OUTPUT_TYPE_JSON;
        }
        
        $this->view = $this->action = trim($this->action, ':@ ');
    }
    
    public function segments()
    {
        $id = $this->id;
        $controller = $this->controller;
        $action = $this->action;
        $view = $this->view;
        $named = $this->named;
        $form = $this->post;
        $files = $this->files;
        $passed = $this->values;
        
        return compact('controller', 'action', 'view', 'id', 'named', 'form', 'files', 'passed');
    }
    
    /**
     * Configures default controller and default action.
     * 
     * @param type $default_controller
     * @param type $default_action 
     */
    public function set_default($default_controller, $default_action)
    {
        $this->default_controller = $default_controller;
        $this->default_action = $default_action;
    }
    
    /**
     *
     * @param string $pattern A regular expression to match a URL
     * @param string $route The destination
     */
    static function add_route($pattern, $route)
    {
        array_unshift(self::$routes, array('pattern' => $pattern, 'route' => $route));
    }
    
    /**
     * 
     * @return string The remapped URL string
     */
    function remap($url)
    {
        if (count(self::$routes) > 0) {
            foreach (self::$routes as $v) {
                if (preg_match($v['pattern'], $url)) {
                    return preg_replace($v['pattern'], $v['route'], $url);
                }
            }
        }
        
        return $url;
    }
}

/**
 * TESTS 
 
$url = 'my_controller/my_action/1/key1:value1/key2:value2/key1/value2';
$_GET= array(
    'url' => $url
);

$_POST = array(
    'name' => 'Igor',
    'place' => 'Barakaldo',
    'age' => '31'
);

echo "<pre>";
echo "Initialization\n===========================\n";

$r = new PewRequest();

var_dump($r->post['name'] === 'Igor');
var_dump($r->get['url'] === 'my_controller/my_action/1/key1:value1/key2:value2/key1/value2');
var_dump($r->post['name'] === 'Igor');

echo "Parsing 1\n===========================\n";

$r->parse($r->get['url']);

var_dump($r->values[0] === '1');
var_dump($r->id === 1);

echo "Parsing 2\n===========================\n";

$r->reset(true);

$r->default_controller = 'the_default_controller';
$r->default_action = 'the_default_action';

$r->parse('');

var_dump($r->controller === 'the_default_controller');
var_dump($r->action === 'the_default_action');

$r->parse('only_controller');

var_dump($r->controller === 'only_controller');
var_dump($r->action === 'the_default_action');

echo "Parsing exception\n===========================\n";

$r->reset();
$r->default_action = null;

try {
    $r->parse('only_controller');
} catch(Exception $e) {
    var_dump('No action segment found [only_controller]' === $e->getMessage());
}

echo "Routing 1\n===========================\n";
$r->reset();

$url = 'only_action';

try {
    $r->add_route('/^(.*)$/', 'my_controller/${1}');
    $url = $r->remap($url);
    $r->parse($url);
    var_dump($r->controller === 'my_controller');
    var_dump($r->action === 'only_action');
} catch(Exception $e) {
    var_dump('No action segment found [only_controller]' === $e->getMessage());
}

echo "Routing 2\n===========================\n";
$r->reset();

$url = '18';
$r->add_route('/^(\d+)$/', 'notes/index/${1}');
$url = $r->remap($url);

try {
    $r->parse($url);
    var_dump($r->controller === 'notes');
    var_dump($r->action === 'index');
    var_dump($r->id === 18);
} catch(Exception $e) {
    var_dump('No action segment found [only_controller]' === $e->getMessage());
}
*/