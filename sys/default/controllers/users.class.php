<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys/default
 */

/**
 * An example Users controller, to demonstrate common login and logout
 * procedures.The basic controller class, with some common methods and fields.
 * 
 * @version 0.2 4-april-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys/default
 */
class Users extends Controller
{
    # no need to use the database, Auth takes over that
    public $use_db = false;
    
    /**
     * The login action displays a login form and validates its input
     *
     * @access public
     */
    public function login()
    {
        # check if a login form was submitted
        if (isset($this->parameters['form'])) {
            # request authentication
            if (Pew::Get('Auth')->authenticate($this->parameters['form'])) {
                # if the referrer was set, redirect there, if not load the default controller and action
                redirect(Pew::Get('Session')->read('referrer', ''));
            } else {
                # login failed, so try again
                Pew::Get('Session')->set_flash('Login failed, please try again');
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
        Pew::Get('Auth')->revoke();
        # and display the default controller/action
        redirect('');
    }
}
