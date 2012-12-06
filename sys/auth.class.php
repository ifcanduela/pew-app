<?php 

/**
 * @package sys
 */

/**
 * User login data and procedures.
 * 
 * @uses PewDatabase Database access is required to retrieve user information
 * @uses Session Session management is made through the Session class
 * @version 0.1 15-mar-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class Auth
{
    /**
     * The database table in which user info is stored.
     * 
     * @access private    
     */
    private $table = 'users';
    
    /**
     * The main field names in the $table.
     * 
     * @access public
     */
    public $fields = array(
        'uuid'  => 'id',            # primary key in the users $table
        'username' => 'username',   # display name in the users $table
        'password' => 'password',   # password field in the users $table
    );
    
    /**
     * Specifies if current session is authenticated; should tie into the
     * active $Session.
     * 
     * @access public
     */
    public $auth = false;
    
    /**
     * Stores current user_id; only available if $auth is true.
     * 
     * @access public
     */
    private $uuid = null;
    
    /**
     * Session data.
     *
     * @var Session
     * @access public
     */
    private $session = null;
    
    /**
     * Database handle.
     *
     * @var PewDatabase
     * @access public
     */
    private $db = null;
    
    public function __construct()
    {
        if (USEAUTH) {
            if (USESESSION) {
                $this->session = Pew::get_session();
            } else {
                throw new Exception("Auth requires sessions enabled");
            }
            
            if (!USEDB) {
                throw new Exception("Auth requires Database access");
            }
            
            $this->auth = $this->session->read('auth', false);
            
            if ($this->session->exists('uuid')) {
                $this->uuid = $this->session->read('uuid');
            }
        }
    }
    
    /**
     * Checks if the session is authenticated.
     *
     * @return bool wheter the session has been previously authenticated
     * @access public
     */
    public function gate()
    {
        return ($this->session->read('auth') == true && $this->session->read('uuid'));
    }
    
    /**
     * Checks if the information provided can be used to start a session.
     *
     * Use the Auth::authenticate() method in the login() action of the users
     * controller (for example), to check whether they can start a session or
     * not.
     *
     * Auth::authenticate() can receive the parameters['form'] property from the
     * controller, thus having access to the fields the user filled in the
     * login form.
     * 
     * @param array $userdata the information the users enter to login
     * @return bool true on login success, false otherwise
     * @access public
     */
    public function authenticate($userdata)
    {
        if (!is_object($this->db)) {
            $this->db = Pew::get_database();
        }

        # find information about the user in the database
        $user = $this->db->where(array($this->fields['username'] => $userdata[$this->fields['username']]))->single($this->table);
        $pass = $this->password($userdata, $user);

        # register the number of login attempts
        $login_attempts = $this->session->read('login_attempts', 0);

        if (is_array($user) && ($user['password'] === $pass)) {
            # if the credentials are correct, set the auth and user_id properties
            $this->auth = true;
            $this->uuid = $user[$this->fields['uuid']];
            $this->session->write('login_attempts', 0);
        } else {
            # if not, return false
            $this->auth = $this->uuid = false;

            # check if the retries should be delayed
            if (defined('LOGIN_DELAY')) {
                if (LOGIN_DELAY > 0) {
                    sleep(LOGIN_DELAY);
                }
            }

            $this->session->write('login_attempts', $login_attempts + 1);
        }
        
        $this->session->write('uuid', $this->uuid);
        $this->session->write('auth', (bool) $this->auth);
        
        return $this->auth;
    }
    
    /**
     * Revoke the authentication status, effectively logging the user out.
     *
     * Use this method in the logout() action of the controller that manages
     * user sessions.
     *
     * @return void
     * @access public
     */
    public function revoke()
    {
        $this->auth = false;
        $this->uuid = false;
        
        $this->session->delete('auth');
        $this->session->delete('uuid');
    }
    
    /**
     * Retrieves the authenticated user info.
     * 
     * @return object the logged-in user information, as an object, or false
     * @access public
     */
    public function user()
    {
        if (!is_object($this->db)) {
            $this->db = Pew::get_database();
        }
        
        if ($this->auth && $this->uuid) {
            # if the session has been authenticated, return user information
            $user = $this->db->where(array($this->fields['uuid'] => $this->uuid))->single($this->table);
            return $user;
        } else {
            # if not, return false
            return false;
        }
    }
    
    /**
     * Changes configuration parameters for the Auth instance.
     * 
     * @param String $property the Auth property to configure
     * @param Mixed $value the value to be assigned to $property
     * @access public
     */
    public function configure($property, $value)
    {
        if (in_array($property, array('fields', 'table', 'nothing_else'), true)) {
            # if the property is in the allowed list, create or update it
            $this->$property = $value;
        } else {
            # if not, trigger a run-time error
            trigger_error('Attempt to initialize non-existent or forbidden property.');
        }
    }
    
    /**
     * Hashes a password using a default algorithm, or a custom_hash function
     * defined elsewhere by the user.
     * 
     * @param array $user_info the user-provided fields from the form
     * @param array $user      the existing user information from the database
     * @access public
     */
    public function password($userdata, $user = null)
    {
        if (function_exists('custom_hash')) {
            # if the custom_hash function has been defined, use it
            return custom_hash($userdata, $user);
        } else {
            # if not, use PHP's crypt function
            if (isset($user)) {
                return crypt($userdata[$this->fields['password']], $user[$this->fields['password']]);
            } else {
                return crypt($userdata[$this->fields['password']]);
            }
        }
    }
}
