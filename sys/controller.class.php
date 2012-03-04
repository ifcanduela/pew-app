<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

/**
 * The basic controller class, with some common methods and fields.
 * 
 * @version 0.21 2-dec-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @abstract
 * @package sys
 */
abstract class Controller
{
    /**
     * Default window title to use in the view.
     *
     * @var string
     * @access protected
     */
    protected $title = APPLICATION_TITLE;
    
    /**
     * Default layout to use in the view.
     *
     * The selected layout must be in app/views/layout/{$layout}.layout.php
     *
     * @var string
     * @access protected
     */
    protected $layout = DEFAULT_LAYOUT;
    
    /**
     * Data created by the action and used by the view.
     *
     * $data is an associative array. Its indices will be converted to
     * variables for easier access inside
     *
     * @var array
     * @access protected
     */
    protected $data = array();

    /**
     * Data submitted by the browser agent via POST method.
     *
     * If no POST data is submitted for the current request, $post will be set
     * to false.
     *
     * @var array
     * @access protected
     */
    protected $post = array();

    /**
     * Data submitted within the URL string in key:value pairs.
     *
     * If no GET data is submitted for the current request, $get will be set to
     * false.
     *
     * @var array
     * @access protected
     */
    protected $get = array();
    
    /**
     * Additional function libraries made available to the controller.
     *
     * $libs is an indexed array that holds the Class names of the 
     * libraries and an associative array that holds the library instances
     * 
     * @var array
     * @access public
     */
    public $libs = array();

    /**
     * The view file to use to render the action result.
     * 
     * Views will be found in app/views/{$controller}/{$view}.php
     *
     * @var string
     * @access public
     */
    public $view = '';
    
    /**
     * Whether to render a view after the action completes.
     *
     * This can be used to render JSON output printed within the action without
     * having to create an additional view file.
     *
     * @var bool
     * @access public
     */
    public $render = true;
    
    /**
     * The text resulting from processing the view
     *
     * @var string
     * @ccess protected
    */
    public $output = '';
    
    /**
     * The intended format of the view output
     *
     * @var string
     * @ccess public
    */
    public $output_type = OUTPUT_TYPE_HTML;
    
    /**
     * Error flag for error pages.
     *
     * @var int
     * @access protected
     */
    protected $error = '';
    
    /**
     * Wheter to require user authentication to complete the action.
     *
     * This way of requiring authentication will be replaced in the future.
     *
     * @var bool
     * @access public
     */
    public $require_auth = false;
    
    /**
     * Whether to instance a database controller or not.
     *
     * @var bool
     * @access protected
     */
    protected $use_db = true;

    /**
     * Whether to instance a database controller or not.
     *
     * @var bool
     * @access protected
     */
    protected $use_twig = USETWIG;
    
    /**
     * Database access object instance.
     *
     * @var Model
     * @access public
     */
    public $model = null;
    
    /**
     * Auth instance.
     *
     * @var Auth
     * @access protected
     */
    protected $auth = null;

    /**
     * Session instance.
     *
     * @var Session
     * @access protected
     */
    protected $session = null;
    
    /**
     * Stores the controller's parameters.
     *
     * @var array
     * @access public
     */
    public $parameters = array();

    /**
     * Base file name of the class.
     *
     * @var string
     * @access private
     */
    protected $file_name = '';

    /**
     * The constructor instantiates the database and populates the instance
     * parameters.
     * 
     * @param array $parameters The URL segments, prepared by the App
     * @return void
     * @access public
     */
    public function __construct($parameters = array())
    {
        # Make sure $model is read through the __get magic method the first time
        unset($this->model);
        
        # Controller file name in the CONTROLLERS folder.
        $this->file_name = class_name_to_file_name(get_class($this));
        
        # Current Session object
        if (USESESSION) {
            $this->session = Pew::Get('Session');
        }
        
        # Current Auth object
        if (USEAUTH) {
            $this->auth = Pew::Get('Auth');
        }
        
        # Function libraries
        # @todo Move this to the __get function
        if ($this->libs) {
            if (is_string($this->libs)) {
                $this->libs = array($this->libs);
            }
            foreach ($this->libs as $library_class_name) {
                $this->libs[$library_class_name] = Pew::GetLibrary($library_class_name);
                
                if ($this->libs[$library_class_name] == false) {
                    Log::in($this->libs[$library_class_name], 'Missing library file');
                }
            }
        }
        
        # Manage the received URL parameters
        if (is_array($parameters)) {
            # Copy the parameters to the controller property.
            $this->parameters = $parameters;
            
            # Simplify access to POST data
            if (isset($parameters['form']) && $parameters['form']) {
                $this->post = $parameters['form'];
            } else {
                $this->post = false;
            }
            
            # Simplify access to named parameters as GET data 
            if (isset($parameters['named']) && count($parameters['named']) !== 0) {
                $this->get = $parameters['named'];
            } else {
                $this->get = false;
            }
        }
        
        if (isset($this->parameters['action'])) {
            # By default, the view name is the same as the action.
            $this->view = $this->parameters['action'];
        } else {
            $this->view = DEFAULT_ACTION;
        }
    }
    
    /**
     * Action is the main decision-maker of the hierarchy, calling the
     * appropriate method of the controller.
     * 
     * This function can be overwritten to modify the behavior or the 
     * function of the parameters, for an example see the Pages controller.
     *
     * @return void
     * @access protected
     * @see Pages
     */
    public function _action()
    {
        # In normal situations, an 'action' parameter exists
        if (isset($this->parameters['action'])) {
            $action =& $this->parameters['action'];
            
            switch ($action{0}) {
                case '_':
                    # Actions prefixed with an underscore are private
                    new PewError(ACTION_FORBIDDEN, $this, $action);
                    break;
                case '@':
                    $this->output_type = OUTPUT_TYPE_XML;
                    # Actions prefixed with an at sign are XML
                    if (file_exists(VIEWS . 'xml' . LAYOUT_EXT)) {
                        $this->layout = 'xml';
                    } else {
                        $this->layout = 'empty';
                    }
                    break;
                case ':':
                    $this->output_type = OUTPUT_TYPE_JSON;
                    # Actions prefixed with a colon are JSON
                    if (file_exists(VIEWS . 'json' . LAYOUT_EXT)) {
                        $this->layout = 'json';
                    } else {
                        $this->layout = 'empty';
                    }
            }
            
            if (!ctype_alpha($action{0})) {
                # Strip the flag character from the action name
                $this->view = $action = substr($action, 1);
            }
            
            if (!method_exists($this, $action)) {
                # If the $action method does not exist, show an error page
                new PewError(ACTION_MISSING, $this, $action);
            }
            
            # Everything's clear pink
            call_user_func_array(array($this, $action), $this->parameters['passed']);
        } else {
            new PewError(404);
        }
    }
    
    /**
     * Initialize the model and library objects when first accessed.
     *
     * @param string $property Controller property to read
     * @return Model The model object
     * @todo Make libraries load lazily instead of in the controller
     */
    public function __get($property)
    {
        if ($property === 'model') {
            # Initialize the model
            if (USEDB && $this->use_db) {
                $this->model = Pew::GetModel(get_class($this), true);

                if ($this->model === false) {
                    Log::in(get_class($this), 'Model not found for this controller, using default');
                    $this->model = new Model($this->file_name);
                }
            } else {
                Log::in(get_class($this), 'Database is disabled for this controller');
                $this->model = null;
            }
            
            return $this->model;
        } elseif (isset($this->libs[$property])) {
            return $this->libs[$property];
        }
        
        return null;
    }
    
    /**
     * Intercept the update of the $require_auth controller property to stop
     * execution of the action as soon as possible.
     *
     * @param string $property Controller property to write
     * @param mixed $value Value to write
     * @return void
     * @access public
     */
    public function __set($property, $value)
    {
        if (USEAUTH && $property === 'require_auth' && $value === true) {
            # check if the user is authenticated
            if (!$this->auth->gate()) {
                # save the current request for later
                $this->session->referrer = $this->parameters['uri'];
                # display the login page
                redirect('users/login');
            }
        }
        
        if ($property === 'model') {
            $this->model = $value;
        }
    }
    
    /**
     * Dispatches the view.
     * 
     * @return void
     * @access protected
     */
    public function _view()
    {
        switch ($this->output_type) {
            case OUTPUT_TYPE_HTML:
                if ($this->use_twig === true) {
                    # Use Twig library to render the view
                    $this->_render_twig();
                } else {
                    $this->_render_html();
                }
                break;

            case OUTPUT_TYPE_JSON:
                $this->_render_json();
                break;

            case OUTPUT_TYPE_XML:
                $this->_render_xml();
                break;
        }

        if (method_exists($this, 'before_render')) {
            $this->before_render();
        }

        $this->_render_layout();
    }
    
    /**
     * Searches for the view file.
     * 
     * @return string The filesystem location of the view.
     * @access protected
     */
    public function _get_view_file()
    {
        # Search in the app/views/{$controller} folder
        if (!file_exists($view_file = VIEWS . $this->file_name . DS . $this->view . VIEW_EXT)) {
            # Search in the sys/default/views/{$controller} folder
            if (!file_exists($view_file = SYSTEM . '/default/views/' . $this->file_name . DS . $this->view . '.php')) {
                $view_file = false;
            }
        }
        
        return $view_file;
    }
    
    /**
     * Renders a Twig template.
     * 
     * @access protected
     */
    public function _render_twig()
    {
        Log::in('Using Twig');
        Twig_Autoloader::register();
        $twig_loader = new Twig_Loader_Filesystem(VIEWS . $this->file_name);
        $twig = new Twig_Environment($twig_loader);
        
        $view_file = $this->_get_view_file() or
                # Show an error page
                new PewError(VIEW_MISSING, $this->parameters['controller'], $this->view);
        $this->output = $twig->render(basename($view_file), $this->data);
    }
    
    /**
     * Renders a standard PHP template.
     * 
     * @access protected
     */
    public function _render_html()
    {
        # Get the view file
        $view_file = $this->_get_view_file();
        
        # Show an error page if the file is not found
        if (!$view_file) {
            new PewError(VIEW_MISSING, $this->parameters['controller'], $this->view);
        }
        
        # Make the variables directly accesible in the template.
        extract($this->data);

        # Load the view into a buffer called $this->output.
        ob_start();
        require $view_file;
        $this->output = ob_get_contents();
        ob_end_clean();
    }
    
    /**
     * Renders a JSON view.
     * 
     * @access protected
     */
    public function _render_json()
    {
        $this->layout = 'empty';
        $this->output = json_encode($this->data);
    }
    
    /**
     * Renders an XML view.
     * 
     * @access protected
     */
    public function _render_xml()
    {
        $this->layout = 'empty';
        $xml = $this->file_name;
        array_to_xml($this->data, $xml, $this->file_name);
        $this->output = $xml->asXml();
    }
    
    /**
     * Renders the layout.
     * 
     * @access protected
     */
    public function _render_layout()
    {
        if (!$this->layout /* || ($this->layout === 'default')*/) {
            # Use the default layout file.
            require SYSTEM . '/default/views/default.layout.php';
        } elseif ($this->layout === 'empty') {
            # output directly
            echo $this->output;
        } else {
            # If the layout .php file cannot be found, show an error page.
            if (!file_exists(VIEWS . $this->layout . '.layout.php')) {
                new PewError(LAYOUT_MISSING, $this->layout);
            }
            # Render the layout file.
            require VIEWS . $this->layout . '.layout.php';
        }
    }
    
    /**
     * Load a snippet into the current view.
     * 
     * @param string $element The snippet to be loaded
     * @param array $element_data Additional variables for use in the template
     * @return void
     * @access public
     */
    public function element($element, $element_data = null)
    {
        # If the element .php file cannot be found, show an error page.
        if (!file_exists(ELEMENTS . $element . '.php')) {
            new PewError(ELEMENT_MISSING, $element);
        }
        # If there are variables, make them easily available to the template.
        if (is_array($element_data)) {
            extract($element_data);
        } elseif (count(func_get_args()) > 1) {
            $args = array_slice(func_get_args(), 1);
            extract($args, EXTR_PREFIX_ALL, 'param');
        }
        # Render the element.
        require ELEMENTS . $element . '.php';
    }
}
