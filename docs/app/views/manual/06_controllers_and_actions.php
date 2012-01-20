<h1>Controllers And Actions</h1>

<p>Controllers also have some tricks up their respective, imaginary
sleeves. Normal operation dictates that a controller must call its
<code class="method">action_name</code> method and then load its
<code class="method">action_name</code> view, but all this can be
overridden, either action by action, by setting some controller
properties, or in a controller-wide fashion, by implementing an
<code class="method">action()</code> method that decides what do.
It's simple enough to work without much thought, but flexible enough
to make your head spin.</p>

<p>All controllers extend the <code class="class">Controller</code>
class, and that gives them PHP superpowers. By default, all of them
have a database connection ready to be abused, using <code
class="php">$this->db</code>. They can also access any URL, Session
or POST value throught the <code
class="php">$this->parameters</code> array. And they can override
default window title or layout by setting the appropriate fields,
either in initialization or inside an <code
class="action">action</code> method, and even inhibit the rendering
of a view, setting <code class="php">$this->render</code> to false,
which may be interesting for ajax, debugging or some other thing you
may come up with. Now you know you can at least do it.</p>

<p>On the other hand, you have a handy <code
class="class">Pages</code> controller that you can extend to your
heart's content. For example, create a <code
class="class">Manual</code> controller that extends <code
class="class">Pages</code>, and you can access quasi-static content
with <code class="url">http://example.com/manual/my-page</code>.
Just make sure to add something similar to this bit of code:</p>

<pre class="brush: php">
class Manual extends Pages
{
    public function _action()
    {
        parent::_action();
    }
}</pre>