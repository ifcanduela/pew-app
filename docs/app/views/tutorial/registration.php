<h2>User registration</h2>

<p>To allow user registration, we'll need to modify the default 
<strong>Users</strong> controller that we copied to our app folder way back
at the beginning. Adding a <code>register()</code> action, along with its 
corresponding view, will allow users to create accounts and submit comments
to our posts. Brace yourself and open 
<code class="file">\blog\app\controllers\users.class.php</code>.</p>

<pre class="brush: php">
&lt;?php

class Users extends Controller
{
    /* ... */
    
    public function register()
    {
        if (isset($this->post['username'])) {
            $new_user = $this->post;
            
            # Check username availability
            $username_exists = $this->model->find_by_username($new_user['username']);
            if ($username_exists) {
                $this->session->set_flash('Invalid username: ' . $new_user['username'] . '; please choose another one.');
                return;
            }
            
            # Check username correctness
            if (!preg_match('/^[A-Za-z0-9_]{4,18}$/', $new_user['username'])) {
                $this->session->set_flash('Invalid username: ' . $new_user['username'] . '; please choose another one.');
                return;
            }
            
            # Check e-mail is not already in use
            $email_exists = $this->model->find_by_email($new_user['email']);
            if ($email_exists || !filter_var($new_user['email'], FILTER_VALIDATE_EMAIL)) {
                $this->session->set_flash('Invalid e-mail address: ' . $new_user['email'] . '; please choose another one.');
                return;
            }
            
            # Check the passwords match
            $passwords_match = $new_user['password'] === $new_user['confirm'];
            if (!$passwords_match) {
                $this->session->set_flash('Passwords do not match; please try again.');
                return;
            }
            
            unset($new_user['confirm']);
            
            $new_user['role'] = 2; // normal user
            $new_user['password'] = $this->auth->password($new_user);
            
            $this->model->save($new_user);
            redirect('');
        }
    }
}
</pre>

<p>Two exciting new things here. We're using the <strong>Session</strong> class
to let the user know about their input errors in the form by using the 
<code>set_flash()</code> method. The other new feature is the <code>find_by_*()</code> 
model methods (and their multi-row equivalents, <code>find_all_by_*()</code>). 
Those allow you to easily find rows filtering by specific columns.</p>

<p>Notice the use of the <code>$auth</code> property to hash the password before 
storing it in the database. When dealing with other users, you should never, ever
store their passwords as plan text. There's legislation about that.</p>

<h3>User registration view</h3>

<p>This is very straightforward:</p>

<pre class="brush: php">
<form action="&lt;?php url('users/register'); ?>" method="POST">
&lt;?php echo $this->session->get_flash('<p>', '</p>'); ?>
    <div>
        <label for="username">Username</label>
        <input type="text" name="username" id="username">
    </div>
    <div>
        <label for="email">E-mail address</label>
        <input type="text" name="email" id="email">
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
    </div>
    <div>
        <label for="confirm">Confirm password</label>
        <input type="password" name="confirm" id="confirm">
    </div>
    <div>
        <input type="submit">
    </div>
</form>
</pre>

<p>Place a link to it somewhere and try it.</p>