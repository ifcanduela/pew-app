<?php

/**
 * @package sys
 */

/**
 * This class encapsulates the view rendering functionality.
 * 
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class View
{
    /**
     * Current request object
     * 
     * @var PewRequest
     * @access public
     */
    public $request = null;

    /**
     * Current request object
     * 
     * @var PewRequest
     * @access public
     */
    public $session = null;
    
    /**
     * Auth object
     * 
     * @var Auth
     * @access public
     */
    public $auth = null;
        
    /**
     * View file name
     * 
     * @var string
     * @access public
     */
    public $view = '';
    
    /**
     * Layout name
     * 
     * @var string
     * @access public
     */
    public $layout = '';

    /**
     * Render the view or not.
     * 
     * @var boolean
     */
    public $render = true;
    
    /**
     * Template data from the controller
     * 
     * @var array
     */
    public $data = null;
    
    /**
     * Base templates directory.
     * 
     * @var string
     * @access public
     */
    public $folder = 'templates';

    /**
     * Template name.
     * 
     * @var string
     * @access public
     */
    public $template = 'index';

    /**
     * Templates file extension.
     * 
     * @var string
     * @access public
     */
    public $extension = '.php';

    /**
     * Default window title to use in the view.
     *
     * @var string
     * @access public
     */
    public $title;
    
    /**
     * 
     */
    public function __construct()
    {
        $this->request = Pew::request();
        $this->session = Pew::session();
    }
    
    /**
     * Finds the view file.
     *
     * @param string $view View file to look for
     * @return string Full filesystem path to the view file
     */
    public function get_view_file($view = null)
    {
        # folder and extension must be configured by the user
        $view_file = $this->folder . $view . $this->extension;
        
        # search in the templates folder
        if (!file_exists($view_file)) {
            $view_file = false;
        }
        
        return $view_file;
    }
    
    /**
     * Finds the layout file.
     * 
     * @return string Full filesystem path to the layout file
     */
    public function get_layout_file()
    {
        # Check for special layouts in XML/JSON requests
        if ($this->request->output_type !== self::OUTPUT_TYPE_HTML) {
            if (file_exists($this->template_dir . $this->request->output_type . Pew::config()->layout_ext)) {
                return VIEWS . $this->request->output_type . Pew::config()->layout_ext;
            } else {
                return null;
            }
        }
        
        # If layout is falsy, use the framework default
        if (!$this->layout) {
            # Use the default layout file.
            return Pew::config()->system_folder . '/default/views/default.layout.php';
        }
        
        # If layout is 'empty', the view does not use a layout
        if ($this->layout === 'empty') {
            return null;
        }
        
        # If the layout .php file cannot be found, show an error page.
        if (!file_exists(Pew::config()->views_folder . $this->layout . Pew::config()->layout_ext)) {
            new PewError(PewError::LAYOUT_MISSING, $this->layout);
        }
        
        # Return the configured layout file
        return Pew::config()->views_folder . $this->layout . Pew::config()->layout_ext;
    }
    
    /**
     * Renders a view according to the request info
     *
     * @param type $data Template data
     * @param type $view View to render
     */
    public function render($data, $view = null)
    {
        $this->data = $data;
        if ($view) {
            $this->view = $view;
        }
        
        # Get the view file
        $view_file = $this->get_view_file();
        
        switch ($this->request->output_type) {
            case OUTPUT_TYPE_HTML: 
                # Show an error page if the file is not found
                if (!$view_file) {
                    new PewError(VIEW_MISSING, $this->request->controller, $this->request->action);
                }
                
                if (USETWIG) {
                    $output = $this->render_twig($view_file, $this->data);
                } else {
                    $output = $this->render_html($view_file, $this->data);
                }
                break;
            case OUTPUT_TYPE_JSON: 
                $output = $this->render_json($this->data);
                break;
            case OUTPUT_TYPE_XML: 
                $output = $this->render_xml($this->data);
                break;
        }
        
        # find the layout file
        $layout_file = $this->get_layout_file();
        
        if (!is_null($layout_file)) {
            if (USETWIG) {
                $output = $this->render_twig($layout_file, array('output' => $output, 'title' => $this->title));
            } else {
                $output = $this->render_html($layout_file, array('output' => $output, 'title' => $this->title));
            }
            echo $output;
        } else {
            echo $output;
        }
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
            return null;
        }
        
        # Make the variables directly accessible in the template.
        extract($template_data);

        # Output the view and save it into a buffer.
        ob_start();
            require $template_file;
            $_template_output = ob_get_contents();
        ob_end_clean();
        
        return $_template_output;
    }
    
    public function render_twig($view_file, $data)
    {
        Log::in('Using Twig');
        
        Twig_Autoloader::register();
        
        $twig_loader = new Twig_Loader_Filesystem(VIEWS . $this->request->controller);
        $twig = new Twig_Environment($twig_loader);
        
        return $twig->render(basename($view_file), $this->data);
    }
    
    public function render_json()
    {
        $this->layout = 'empty';
        return $this->output = json_encode($this->data);
    }
    
    public function render_xml()
    {
        $this->layout = 'empty';
        $this->output = array_to_xml($this->data, $this->request->controller);
        return $this->output;
    }
    
    public function exists()
    {
        return file_exists(Pew::config()->views_folder . $this->request->controller . DS . $this->request->action . Pew::config()->view_ext);
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
        if (isset($folder)) {
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
        if (isset($template)) {
            if (file_exists($this->folder . $template . $this->extension)) {
                $this->template = $template;
            } else {
                return false;
            }
        }

        return $this->template;
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
        if (!file_exists(Pew::config()->elements_folder . $element . Pew::config()->element_ext)) {
            new PewError(PewError::ELEMENT_MISSING, $element);
        }

        # If there are variables, make them easily available to the template.
        if (is_array($element_data)) {
            extract($element_data);
        } elseif (count(func_get_args()) > 1) {
            $args = array_slice(func_get_args(), 1);
            extract($args, EXTR_PREFIX_ALL, 'param');
        }
        
        # Render the element.
        require Pew::config()->elements_folder . $element . Pew::config()->element_ext;
    }
}
