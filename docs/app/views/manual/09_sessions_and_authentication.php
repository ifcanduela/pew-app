<h1>Sessions And Authentication</h1>

<p>Sessions are easy to use, because that's how it must be. You can
write or read session variables with the <code
class="method">Session::write()</code> and <code
class="method">Session::read()</code> static methods, and check if
a key has been set with <code class="method">Session::exists()
</code>. A single informational message can be stored and
retrieved with <code class="method">Session::setFlash()</code>
and <code class="method">Session::getFlash()</code>, and
anything you want can be obliterated with
<code class="method">Session::delete()</code>. That's all there is
to it, really.</p>

<p>The <code class="method">read()</code> method accepts a second
parameter after the <code class="variable">$key</code> to read, that
allows you to return a default value if said <code
class="variable">$key</code> hasn't been defined. Handy for me.</p>

<h2>Identify Your Hoominz</h2>

<p>The <code class="class">Auth</code> class is very much like
<code class="class">Session</code>, but with a more specific purpose
and some extra tricks. You can define your own hashing function in
<code class="filesystem">app/config/bootstrap.php</code>, name it
<code class="php">custom_hash()</code>, and see the
<code class="method">Auth::authenticate()</code> method
automatically use it instead of the default SHA1 algorithm:</p>

<pre class="brush: php">
function custom_hash($userdata)
{
    # $userdata contains all required fields
    return md5($userdata['username']
           . ':'
           . sha1($userdata['password']));
}</pre>

<p>The <code class="class">Auth</code> class expects a database
to be configured, with a table called <code>users</code>, with
fields called <code>id</code>, <code>username</code> and
<code>password</code>. If a field does not match your database,
you can change the fields <code class="class">Auth</code> uses by
calling <code class="method">Auth::configure()</code> in
<code class="filesystem">app/config/bootstrap.php</code>.</p>

<p>The <code class="method">Auth::authenticate()</code> and
<code class="method">Auth::gate()</code> methods are used to
start an authenticated session and allow the authenticated users
to start an action, respectively. They are like the mother and
the father of the actions. More or less. You should not have to use
<code class="method">Auth::gate()</code> yourself, instead relying
on using <code class="method">Auth::authenticate()</code> in the
<code class="action">login</code> action of your <code
class="class">Users</code> controller.</p>
