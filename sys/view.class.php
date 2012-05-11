<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

/**
 * This class encapsulates the view rendering functionality.
 * 
 * @version 0.5 29-apr-2012
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class View
{
    /**
     * Current request object
     * 
     * @var PewRequest
     */
    public $request = null;

    /**
     * Current request object
     * 
     * @var PewRequest
     */
    public $session = null;
    
    /**
     * Auth object
     * 
     * @var Auth
     */
    public $auth = null;
        
    /**
     * View file name
     * 
     * @var string
     */
    public $view = DEFAULT_ACTION;
    
    /**
     * Layout name
     * 
     * @var string
     */
    public $layout = DEFAULT_LAYOUT;
    
    /**
     * Template data from the controller
     * 
     * @var array
     */
    public $data = null;
    
    /**
     * @param PewRequest $request 
     */
    public function __construct(PewRequest $request)
    {
        $this->request = $request;
        $this->session = Pew::Get('Session');
        $this->auth = Pew::Get('Auth');
    }
    
    /**
     * Finds the view file.
     * 
     * @return string Full filesystem path to the view file
     */
    public function get_view_file()
    {
        $view_file = false;
        
        # Search in the app/views/{$controller} folder
        if (!file_exists($view_file = VIEWS . $this->request->controller . DS . $this->view . VIEW_EXT)) {
            # Search in the sys/default/views/{$controller} folder
            if (!file_exists($view_file = SYSTEM . '/default/views/' . $this->request->controller . DS . $this->view . '.php')) {
                $view_file = false;
            }
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
        if ($this->request->output_type !== OUTPUT_TYPE_HTML) {
            if (file_exists(VIEWS . $this->request->output_type . LAYOUT_EXT)) {
                return VIEWS . $this->request->output_type . LAYOUT_EXT;
            } else {
                return null;
            }
        }
        
        # If layout is falsy, use the framework default
        if (!$this->layout) {
            # Use the default layout file.
            return SYSTEM . '/default/views/default.layout.php';
        }
        
        # If layout is 'empty', the view does not use a layout
        if ($this->layout === 'empty') {
            return null;
        }
        
        # If the layout .php file cannot be found, show an error page.
        if (!file_exists(VIEWS . $this->layout . LAYOUT_EXT)) {
            new PewError(LAYOUT_MISSING, $this->layout);
        }
        
        # Return the configured layout file
        return VIEWS . $this->layout . LAYOUT_EXT;
    }
    
    /**
     * Renders a view according to the request info
     *
     * @param type $view View to render
     * @param type $data Template data
     */
    public function render($view, $data)
    {
        $this->view = $view;
        $this->data = $data;
        
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
        return file_exists(VIEWS . $this->request->controller . DS . $this->request->action . VIEW_EXT);
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
