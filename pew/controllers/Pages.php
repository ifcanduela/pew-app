<?php

namespace pew\controllers;

/**
 * The Pages controller can serve static views, useful for help or about pages.
 * 
 * @package pew/default/controllers
 * @author ifcanduela <ifcanduela@gmail.com>
 */
class Pages extends \pew\Controller
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
     * @access public
     */
    public function _action()
    {
        # the title for the view
        if ($this->request->segment(1)) {
            $this->view->title = ucwords(str_replace('_', ' ', $this->request->segment(1)));
        }
    }
}
