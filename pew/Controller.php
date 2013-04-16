<?php

namespace pew;

use pew\libs;

/**
 * The basic controller class, with some common methods and fields.
 * 
 * @package pew
 * @author ifcanduela <ifcanduela@gmail.com>
 * @abstract
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
     */
    public $data = array();

    /**
     * Data submitted by the browser agent via POST method.
     *
     * If no POST data is submitted for the current request, $post will be set
     * to false.
     *
     * @var array
     */
    protected $post = array();

    /**
     * Data submitted within the URL string in key:value pairs.
     *
     * If no GET data is submitted for the current request, $get will be set to
     * false.
     *
     * @var array
     */
    protected $get = array();
    
    /**
     * Additional function libraries made available to the controller.
     *
     * $libs is an indexed array that holds the Class names of the 
     * libraries and an associative array that holds the library instances
     * 
     * @var array
     */
    public $libs = array();
    
    /**
     * The view file to use to render the action result.
     * 
     * Views will be found in app/views/{$controller}/{$view}.php
     *
     * @var View
     */
    public $view = null;
    
    /**
     * Whether to render a view after the action completes.
     *
     * This can be used to render JSON output printed within the action without
     * having to create an additional view file.
     *
     * @var bool
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
     */
    protected $error = '';
    
    /**
     * Wheter to require user authentication to complete the action.
     *
     * This way of requiring authentication will be replaced in the future.
     *
     * @var bool
     */
    public $require_auth = false;
    
    /**
     * Whether to instance a database controller or not.
     *
     * @var bool
     */
    protected $use_db = true;
    
    /**
     * Database access object instance.
     *
     * @var Model
     */
    public $model = null;
    
    /**
     * The request information. 
     * 
     * @var Request
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
     */
    public $auth = null;

    /**
     * Session instance.
     *
     * @var Session
     */
    public $session = null;
    
    /**
     * Stores the controller's parameters.
     *
     * @var array
     */
    public $parameters = array();

    /**
     * Base file name of the class.
     *
     * @var string
     */
    protected $file_name = '';

    /**
     * The constructor instantiates the database and populates the instance
     * parameters.
     * 
     * @param pew\libs\Request $request The request information
     * @return void
     */
    public function __construct(\pew\libs\Request $request, $view = false)
    {
        # Assign Request and View objects
        $this->request = $request;

        if ($view) {
            $this->view = $view;
        } else {
            $this->view = Pew::view();
        }
        
        # Make sure $model is read through the __get magic method the first time
        unset($this->model);
        unset($this->auth);
        unset($this->session);
        
        # Controller file name in the /views/ folder.
        $this->file_name = class_name_to_file_name(
                join('', 
                    array_slice(
                        explode('\\', get_class($this)), 
                        -1
                    )
                )
        );

        # Global action prefix override
        if (!$this->action_prefix && Pew::config()->action_prefix) {
            $this->action_prefix = Pew::config()->action_prefix;
        }
        
        # Function libraries
        # @todo Move this to the __get function
        if (is_array($this->libs)) {
            foreach ($this->libs as $library_class_name) {
                $lib = Pew::library($library_class_name);
                
                if ($lib === false) {
                    throw new \RuntimeException("Library $library_class_name cound not be found.");
                }

                $this->libs[$library_class_name] = $lib;
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
     */
    public function _action($action, $parameters)
    {
        if (!method_exists($this, $this->action_prefix . $action)) {
            # If the $action method does not exist, show an error page
            new controllers\Error(Pew::request(), controllers\Error::ACTION_MISSING, $this, $this->action_prefix . $action);
        }

        # set default template before calling the action
        $this->view->template($this->file_name . '/' . $action);
        $this->view->title = $action;

        # everything's clear pink
        $view_data = call_user_func_array(array($this, $this->action_prefix . $action), $parameters);

        if (!is_array($view_data)) {
            $view_data = compact('view_data');
        }

        return $view_data;
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
            if ($this->use_db) {
                $this->model = Pew::model(get_class($this), true);

                if ($this->model === false) {
                    Log::in(get_class($this), 'Model not found for this controller, using default');
                    $this->model = new Model($this->file_name);
                }
            } else {
                Log::in(get_class($this), 'Database is disabled for this controller');
                $this->model = null;
            }
            
            return $this->model;
        } elseif ($property === 'session') {
            $this->session = \pew\Pew::session();
            return $this->session;
        } elseif ($property === 'auth') {
            $this->auth = \pew\Pew::auth();
            return $this->auth;
        } elseif ($property === '') {
            $this->request = \pew\Pew::request();
            return $this->request;
        }
        
        return null;
    }
}
