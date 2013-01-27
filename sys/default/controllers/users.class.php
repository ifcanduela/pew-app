<?php

/**
 * An example Users controller, to demonstrate common login and logout
 * procedures.The basic controller class, with some common methods and fields.
 * 
 * @package pew/default/controllers
 * @author ifcanduela <ifcanduela@gmail.com>
 */
class Users extends Controller
{
    /**
     * The login action displays a login form and validates its input
     *
     * @access public
     */
    public function login()
    {
        # check if a login form was submitted
        if ($this->post) {
            # request authentication
            if ($this->auth->authenticate($this->post)) {
                # if the referrer was set, redirect there, if not load the default controller and action
                redirect($this->session->read('referrer', ''));
            } else {
                # login failed, so try again
                $this->session->set_flash('Login failed, please try again');
            }
        }
        
        # the login view will be displayed if execution reaches this point
    }
    
    /**
     * The logout action resets the authentication for the current session.
     *
     * @access public
     */
    public function logout()
    {
        # clear authentication status
        $this->auth->revoke();
        # and display the default controller/action
        redirect('');
    }
}
