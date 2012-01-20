<div class="tutorial_navigation">
    <a href="<?php url('tutorial/02'); ?>" class="prev">&laquo; tables and tables of data</a>
    <a href="<?php url('tutorial/04'); ?>" class="next">A nice view &raquo;</a>
</div>

<h2>A controller</h2>

<p>And I'm not talking about your wife.</p>

<p>We're ready to start writing the application logic. It's usually
put in classes called <em>controllers</em>. To manage blog posts,
we'll create a Posts controller.</p>

<p>Create a new file called
<code class="file">posts.class.php</code> in
<code class="file">blog/app/controllers/</code>, and add this:</p>
<pre class="brush: php">
&lt;?php

# Posts controller
class Posts extends Controller
{
    public function index()
    {
        $this->data['posts'] = $this->model->order_by('date DESC')->find_all();
    }
}
</pre>

<p>Right there, we're fetching all rows from the posts table in the
database, and passing them to the <em>view</em>, which we'll create
next. You pass data to the view by adding indexes to the
controller's <code>data</code> array. In this example, the posts
will be available in a variable called <code>$posts</code>.</p>

<p>Notice the use of the <code>$model</code> member of the Posts class. That's
an automatically-created database accessor. It's configured by default to
read from and write to a table by the same name as the file the posts
controller is defined in.</p>

<div class="tutorial_navigation">
    <a href="<?php url('tutorial/02'); ?>" class="prev">&laquo; tables and tables of data</a>
    <a href="<?php url('tutorial/04'); ?>" class="next">A nice view &raquo;</a>
</div>