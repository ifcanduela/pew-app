<h2>Controllers</h2>

<p>Controllers are classes that contain the logic of the application, grouped
by <em>domain</em> o model. In simpler terms, that means that for every kind of
object you application manages, you'll have a controller. Controllers come in
two flavours: the <em>action controller</em> and the <em>pages
controller</em>.</p>

<p>The pages controller is a simple gateway to display static-content web pages.
Of course, those pages are still within the framework, and can access the
functions and parameters of the application. <em>Pages</em> are actually
<em>views</em> sharing the same, anonymous <em>action</em>.</p>

<p>Action controllers are defined, of course, by their actions. A
<strong>Cat</strong> controller can have a <code>eat()</code> action, a
<code>pounce()</code> action and a <code>sleep()</code> action. Actions are mapped
to URLs in such a way that adding the name of an action after the name of a
controller in a URL will make the framework perform said action. For example,
the URL <code class="file">http://i-love-ca.ts/cats/pounce/Whiskers</code>
could be mapped to this action:</p>

<pre class="brush: php">
&lt;?php

class Cats extends Controller
{
    function pounce($cat_name)
    {
        $this->data['phrase'] = "$cat_name has pounced!!";
    }
}
</pre>

<p>In that case, and supposing a <code class="file">pounce.php</code> view has been
created, the user would see the message according to the action <code>pounce()</code>
and the parameter <code>Whiskers</code>. But how do those parameters work?</p>

<h3>Passing data to controller actions</h3>

<p>Actions receive parameters in various ways, but the most common is through URL
segments. In the above example we've added a parameter, <code>Whiskers</code>, after
the controller and the action, and that string was assigned to the function
argument <code>$cat_name</code> automatically. Any subsequent segment (separated by
a slash (<code>/</code>) will be passed as an additional parameter to the function,
with the exception of <em>named segments</em>. This is an example of a
complex URL with a named segment:</p>

<pre>http://localhost/cats/pounce/1/sort:name/brown</pre>

<p>The action for such URL could be defined thus:</p>

<pre class="brush: php">
&lt;?php

class Cats extends Controller
{
    function view_siblings($id, $color)
    {
        
    }
}
</pre>

<p>Huh? The <code>1</code> maps to the <code>$id</code> argument, but how do
that <code>sort:name</code> and the color fit into it? Segments with a colon in them are
<em>named segments</em> and are treated like GET HTTP query strings (that is, as if the
request had been <code>?sort=name&id=1&color=brown</code>). They are not passed
as action arguments, instead being accessible like this:</p>

<pre class="brush: php">
&lt;?php

$this->get['sort'];
</pre>

<h3>Receiving form data</h3>

<p>These days, a lot of input data in web applications comes in POST format, be
it from HTML forms or from AJAX requests (or other forms of REST). In Pew-Pew-Pew,
that data is quite easy to handle:</p>

<pre class="brush: php">
&lt;?php

class Cat extends Controller
{
    function rename($id)
    {
        if ($this->post) {
            $this->model->save($this->post);
        }
        
        $this->data['cat'] = $this->model->find($id);
    }
}
</pre>

<p>The action can check if there's POST data simply by reading <code>$this->post</code>,
which is false when no POST data is present. If there is such data, <code>$post</code> will
be an associative array with all fields submitted by the form.</p>

<h3>Passing data to views</h3>

<p>Every controller has a <code>$data</code> associative array that holds every value
you want to make available to the view. Indexes of <code>$data</code> will become variables
in the view. In the previous <code>rename()</code> example, <code>$cat</code> would be
accessible in the <code class="file">/app/views/cats/rename.php</code> view file:</p>

<pre class="brush: php">
&lt;?php

echo $cat['name'];
</pre>

<h3>Controller properties</h3>

<p>Some additional controller properties are <code>$require_auth</code>, which makes
the controller unavailable to anonymous visitors, <code>$use_db</code>, that can
be set to false on controllers without model (that is, with no related database
table), or <code>$session</code>, that provides a simple interface to handle
session data.</p>

<p>There's also the <code>$libs</code> array, that holds class names that are
automatically instantiated and made available as properties of the controller:</p>

<pre class="brush: php">
&lt;?php

class Cats extends Controller
{
    public $libs = array('Markdown', 'MyOwnLibrary');
    
    function pounce()
    {
        $formatted_text = $this->Markdown->process('**cats!**');
    }
}
</pre>

<p>Sometimes it's useful to subvert the behaviour of the underlying implementation. By
setting some controller properties inside an action you can, for example, use a
view other than the one named after the action, or even inhibit completely the
rendering of a view:</p>

<pre class="brush: php">
&lt;?php

class Cats extends Controller
{
    function sleep($cat_id)
    {
        # this would change the view from 'sleep' to 'rest'
        $this->view = 'nap';
        
        # we can also change which layout is used
        $this->layout = 'alternate';
        
        # and this would prevent the view and layout from showing up at all
        $this->render = false;
        
        # but you cat output something else, for example the JSON
        # representation of the cat
        $cat = $this->model->find($cat_id);
        echo json_encode($cat);
    }
}
</pre>

<p>Now is a good time to mention the <code>$model</code> property. In the next chapter we
will learn about models, as this property is the gateway from the controller to the
database.</p>

<p>Finally, the controller has some other useful properties, like <code>$title</code>,
that holds the page title that is displayed in the browser tab, or
<code>$output_type</code>, that controls whether HTML, XML or JSON will be produced
as output.</p>