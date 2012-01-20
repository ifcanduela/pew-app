<?php

/**
 * The View class manages rendering of controller output
 * 
 * @version 0.1 4-jan-2012
 * @author ifernandez <ifcanduela@gmail.com>
 */
class View
{
    public $layout = DEFAULT_LAYOUT;
    public $view_file = null;
    public $output_type = OUTPUT_TYPE_HTML;
    
    function __construct($view, $output_type = OUTPUT_TYPE_HTML)
    {
        $this->output_type = $output_type;
        
        switch ($this->output_type) {
            case OUTPUT_TYPE_HTML:
                echo "HTML";
                break;
            case OUTPUT_TYPE_JSON:
                echo "JSON";
                break;
            case OUTPUT_TYPE_XML:
                echo "XML";
                break;
            default:
                echo "asda";
        }
    }
}