<?php

/**
 * This class encapsulates the view rendering functionality.
 * 
 * @package pew
 * @author ifcanduela <ifcanduela@gmail.com>
 */
class View
{
    /**
     * View helpers, accessed through __get and __set.
     * 
     * @var array
     * @access protected
     */
    protected $helpers = array(
        'session' => null,
        'auth' => null,
        'log' => null,
        'request' => null,
    );
    
    /**
     * Render the view or not.
     * 
     * @var boolean
     */
    public $render = true;
    
    /**
     * Enable use of the Twig templating engine.
     * 
     * @var boolean
     */
    public $twig = false;
    
    /**
     * Base templates directory.
     * 
     * @var string
     * @access public
     */
    protected $folder = 'app/views/';

    /**
     * Template name.
     * 
     * @var string
     * @access public
     */
    protected $template = 'index';
    
    /**
     * Layout name.
     * 
     * @var string
     * @access public
     */
    protected $layout= 'index';

    /**
     * Templates file extension.
     * 
     * @var string
     * @access public
     */
    protected $extension = '.php';

    /**
     * Default window title to use in the view.
     *
     * @var string
     * @access public
     */
    public $title = '';
    
    /**
     * @access public
     */
    public function __construct(array $helpers = array())
    {
        foreach ($helpers as $key => $value) {
            $this->helpers[$key] = $value;
        }
    }
    
    /**
     * Renders a view according to the request info
     *
     * @param type $data Template data
     * @param type $view View to render
     */
    public function render($data, $template = null)
    {
        if (!$template) {
            $template = $this->template;
        }
        
        # Get the view file
        $template_file = $this->folder() . $template . $this->extension();
        
        if (!$template_file) {
            throw new Exception("Template file not found: $template_file");
        }
        
        if ($this->twig) {
            $output = $this->render_twig($template_file, $data);
        } else {
            $output = $this->render_html($template_file, $data);
        }
        
        return $output;
    }
    
    /**
     * Renders a standard PHP template.
     * 
     * @return string The output from the rendered view file
     * @access protected
     */
    public function render_html($template_file, $template_data)
    {
        # Return null if the view file does not exist
        if (!file_exists($template_file)) {
            var_dump(debug_backtrace());
            throw new Exception("Template file does not exist: $template_file");
        }
        
        # Make the variables directly accessible in the template.
        extract($template_data);

        # Output the view and save it into a buffer.
        ob_start();
            require $template_file;
            $template_output = ob_get_contents();
        ob_end_clean();
        
        return $template_output;
    }
    
    public function render_twig($view_file, $data)
    {
        Twig_Autoloader::register();
        
        $twig_loader = new Twig_Loader_Filesystem(VIEWS . $this->request->controller);
        $twig = new Twig_Environment($twig_loader);
        
        return $twig->render(basename($view_file), $this->data);
    }
    
    public function render_json()
    {
        return json_encode($this->data);
    }
    
    public function render_xml()
    {
        return array_to_xml($this->data, $this->request->controller);
    }
    
    public function exists($template = null)
    {
        return file_exists($this->folder() . $template . $this->extension());
    }

    /**
     * Set and get the templates folder.
     *
     *  Always includes a trailing slash (OS-dependent)
     * 
     * @param string $folder Folder where templates should be located
     * @return string Folder where templates should be located
     */
    public function folder($folder = null)
    {
        if (!is_null($folder)) {
            if (is_dir($folder)) {
                $this->folder = trim($folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            } else {
                return false;
            }
        }

        return trim($this->folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Set and get the templates to render.
     * 
     * @param string $template Name of the template
     * @return string Name of the template
     */
    public function template($template = null)
    {
        if (!is_null($template)) {
            if (file_exists($this->folder() . $template . $this->extension())) {
                $this->template = $template;
            } else {
                return false;
            }
        }

        return $this->template;
    }

    /**
     * Set and get the view file extension.
     * 
     * @param string $extension View file extension
     * @return string View file extension
     */
    public function extension($extension = null)
    {
        if (!is_null($extension)) {
            $this->extension = '.' . ltrim($extension, '.');
        } else {
            return false;
        }

        return '.' . ltrim($this->extension, '.');
    }

    /**
     * Set and get the layout to use.
     * 
     * @param string $layout Name of the layout
     * @return string Name of the layout
     */
    public function layout($layout = null)
    {
        if (!is_null($layout)) {
            if (file_exists($this->folder() . $layout . $this->extension())) {
                $this->layout = $layout;
            } else {
                return false;
            }
        }

        return $this->layout;
    }

    /**
     * Set and get the view title.
     * 
     * @param string $title The title of the view
     * @return string The title of the view
     */
    public function title($title = null)
    {
        if (!is_null($title)) {
            $this->title = $title;
        }

        return $this->title;
    }
    
    /**
     * Load and outputs a snippet into the current view.
     * 
     * @param string $element The snippet to be loaded, relative to the templates folder
     * @param array $element_data Additional variables for use in the element
     * @return void
     * @access public
     */
    public function element($element, $element_data = null)
    {
        $element_file = $this->folder() . $element . $this->extension();
        
        # If the element .php file cannot be found, show an error page.
        if (!file_exists($element_file)) {
            throw new Exception("The element file $element_file ould not be found.");
        }

        # If there are variables, make them easily available to the template.
        if (is_array($element_data)) {
            extract($element_data);
        } elseif (count(func_get_args()) > 1) {
            $args = array_slice(func_get_args(), 1);
            extract($args, EXTR_PREFIX_ALL, 'param');
        }
        
        # Render the element.
        require $element_file;
    }
    
    public function __get($key)
    {
        if (!array_key_exists($key, $this->helpers)) {
            debug_print_backtrace();
            throw new InvalidArgumentException("$key index not found in helpers array.");
        }
        
        return $this->helpers[$key];
    }
    
    public function __set($key, $value)
    {
        $this->helpers[$key] = $value;
    }
}
