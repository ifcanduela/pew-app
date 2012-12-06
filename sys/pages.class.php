<?php

/**
 * @package sys/default
 */

/**
 * The Pages controller can serve static views, useful for help or about pages.
 * 
 * @version 0.5 13-mar-2012
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys/default
 */
class Pages extends Controller
{
    /**
     * Whether to instance a database controller or not.
     *
     * Override the default value of the $use_db property in Controller because
     * Pages does not need database access trough a Model.
     *
     * @var boolean
     * @access protected
     */
    protected $use_db = false;
    
    /**
     * The action method of the Pages controller overwrites the same method of
     * the Controller class, to use the action parameter as a view parameter.
     *
     * Instead of this:  /pages/view/my-view-name
     * The url would be: /pages/my-view-name
     * 
     * @author ifcanduela <ifcanduela@gmail.com>
     * @version 0.2 31-march-2011
     * @access public
     */
    public function _action()
    {
        if (is_dir(VIEWS . $this->view_folder . DS . $this->parameters['action'])) {
            if (file_exists(VIEWS . $this->view_folder . DS . $this->parameters['action'] . DS . $this->parameters[0]) . '.php') {
                $this->view = $this->parameters['action'] . DS . $this->parameters[0];
            }
        }
        
        # the title for the view
        $view_title = ucwords(str_replace('_', ' ', $this->parameters['action']));
        $title = $this->title = "$view_title » " . APPLICATION_TITLE;
    }
}
