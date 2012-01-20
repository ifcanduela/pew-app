<h2>The Posts controller</h2>

<p>You should experience some kind of climax during this chapter, because
we're going to do <em>programming stuff</em>.</p>

<p>Create <code class="file">/app/controllers/posts.class.php</code> and
type this:</p>

<pre class="brush: php">
&lt;?php

class Posts extends Controller
{
    public function index()
    {
        $this->data['posts'] = $this->model->find_all();
    }
}
</pre>
    
<p>Wowowowohoho, stop there, chief! There's a loy going on in there, and
some of it is <em>magic</em>. For starters, this will make the URL
<a href="http://localhost/blog/">http://localhost/blog/</a> stop throwing
<code>CONTROLLER MISSING</code> errors. It will actually display a
<em>different</em> error: <code>VIEW MISSING</code>. Don't scramble.</p>

<p>Most of your controllers will be defined this way. This makes the
controller an <em>action controller</em>, that always fires one of the
methods you define inside it (i.e. <code>index()</code>).</p>

<p>There are other types of controllers. This documentation, for example,
is built using the Pages controller, which does not use actions, instead 
automatically loading a view, and it's useful for "about" pages or other
types of static content. Pages controllers are declared by extending from
<code>Pages</code> instead of <code>Controller</code>.</p>

<h3>Customizing controllers</h3>

<p>The last option for controllers is extending <code>PewController</code>.
PewController is a mechanism for extending the base <code>Controller</code>
class and affect multiple controllers in your application at the same time.
Creating PewControllers is easy, but convoluted at the same time. Do this:</p>

<ol>
    <li>Create <code class="file">/app/pew_controller.class.php</code></li>
    <li>Type <code>&lt;?php class PewController extends Controller {}</code>
    inside the file and save it</li>
    <li>Have your controllers extend PewController instead of simply Controller</li>
</ol>

<p>Modify PewController to your heart's content. Add functions that you find
yourself needing in more than one controller, or implement some of the
automatically-called hooks:</p>

<pre class="brush: php">
&lt;?php

class PewController extends Controller
{
    public function before_action()
    {
        // this can be used to create data before the action is run
    }
    
    public function after_action()
    {
        // this is useful to modify view settings
    }
    
    public function before_render()
    {
        // this is called before rendering the view
    }
}
</pre>

<p>Those callback hooks will be automatically used in every controller that
extends PewController, so be cautious.</p>

<p>A last option that I will mention only because it's possible, is to
create a normal action controller and make another action controller extend
from it. For example, you may have an Employee controller and define a
Boss controller with <code>class Boss extends Employee</code>. Just a
possibility, not that I've had any use for it ever.</p>
