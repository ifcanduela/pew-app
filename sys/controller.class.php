<?php

/**
 * @package sys
 */

/**
 * The basic controller class, with some common methods and fields.
 * 
 * @version 0.31 29-apr-2012
 * @author ifcanduela <ifcanduela@gmail.com>
 * @abstract
 * @package sys
 */
abstract class Controller
{
    /**
     * Data created by the action and used by the view.
     *
     * $data is an associative array. Its indices will be converted to
     * variables for easier access inside
     *
     * @var array
     * @access public
     */
    public $data = array();

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
     * @var View
     * @access public
     */
    public $view = null;
    
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
    protected $use_twig = false;
    
    /**
     * Database access object instance.
     *
     * @var Model
     * @access public
     */
    public $model = null;
    
    /**
     * The request information. 
     * 
     * @var PewRequest
     * @access public
     */
    public $request = null;

    /**
     * String prefixed to action names in this controller.
     * 
     * @var string
     */
    protected $action_prefix = '';
    
    /**
     * Auth instance.
     *
     * @var Auth
     * @access public
     */
    public $auth = null;

    /**
     * Session instance.
     *
     * @var Session
     * @access public
     */
    public $session = null;
    
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
     * @param PewRequest $request The request information
     * @return void
     * @access public
     */
    public function __construct($request, $view = false)
    {
        # Assign Request and View objects
        $this->request = $request;
        $this->view = $view;
        
        # Make sure $model is read through the __get magic method the first time
        unset($this->model);
        
        # Controller file name in the CONTROLLERS folder.
        $this->file_name = class_name_to_file_name(get_class($this));
        $this->view_folder = $request->controller;

        # Global action prefix override
        if (!$this->action_prefix && Pew::config()->action_prefix) {
            $this->action_prefix = Pew::config()->action_prefix;
        }
        
        # Function libraries
        # @todo Move this to the __get function
        if ($this->libs) {
            if (is_string($this->libs)) {
                $this->libs = array($this->libs);
            }
            foreach ($this->libs as $library_class_name) {
                $this->libs[$library_class_name] = Pew::get_library($library_class_name);
                
                if ($this->libs[$library_class_name] === false) {
                    Log::in($this->libs[$library_class_name], 'Missing library file');
                }
            }
        }
        
        $parameters = $request->segments();
        
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

            $this->get = array();

            # Simplify access to named parameters as GET data 
            if (isset($parameters['named']) && count($parameters['named']) !== 0) {
                $this->get += $parameters['named'];
            }

            # Add proper GET data from the URL
            if ($request->get) {
                $this->get += $request->get;
            }

            if (empty($this->get)) {
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
                    if (file_exists(VIEWS . 'xml' . Pew::config()->layout_ext)) {
                        $this->layout = 'xml';
                    } else {
                        $this->layout = 'empty';
                    }
                    break;
                case ':':
                    $this->output_type = OUTPUT_TYPE_JSON;
                    # Actions prefixed with a colon are JSON
                    if (file_exists(VIEWS . 'json' . Pew::config()->layout_ext)) {
                        $this->layout = 'json';
                    } else {
                        $this->layout = 'empty';
                    }
            }
            
            if (!ctype_alpha($action{0})) {
                # Strip the flag character from the action name
                $this->view = $action = substr($action, 1);
            }
            
            if (!method_exists($this, $this->action_prefix . $action)) {
                # If the $action method does not exist, show an error page
                new PewError(PewError::ACTION_MISSING, $this, $this->action_prefix . $action);
            }
            
            # Everything's clear pink
            $view_data = call_user_func_array(array($this, $this->action_prefix . $action), $this->parameters['passed']);
            return $view_data;
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
                $this->model = Pew::get_model(get_class($this), true);

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
}
