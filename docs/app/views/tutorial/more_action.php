<h2>More action</h2>

<p>Let's quickly create edit and add actions:</p>

<pre class="brush: php">
&lt;?php

class Posts extends Controller
{
    /* ... */
    
    public function edit($id)
    {
        if ($this->post) {
            $this->model->save($this->post);
            redirect('posts/view/' . $this->post['id']);
        }
        
        $this->data['post'] = $this->model->find($id);
    }
}
</pre>
    
<p>Due to an unfortunate coincidence in nomenclature, it's easy to
mistake the controller's <code>$post</code> property with a blog post, which
it is not. The <code>$post</code> property contains the data submitted via
HTTP POST method using an HTML form. We can save it directly to the database
using the method <code>save()</code> provided by the model.</p>

<p>If no form was submitted, <code>$this->post</code> contains false, so
we fetch the post we want to edit using the model's <code>find()</code>
method and pass it to the view by setting an index named
<code>'post'</code> in the controller's <code>$data</code> array. The
fetched data is returned as an associative array, and will be available
in the view with the variable name <code>$post</code>.</p>

<p>The <code>add()</code> action is even simpler:</p>

<pre class="brush: php">
&lt;?php

class Posts extends Controller
{
    /* ... */
    
    public function add()
    {
        if ($this->post) {
            $post = $this->model->save($this->post);
            redirect('posts/view/' . $post['id']);
        }
    }
}
</pre>
    
<p>I've spiced thing up a little, but it's simple as they come. Again, we
check if a form has been submitted. If indeed it has, we save it, but this
time we hold its return value, because the <code>save()</code> return the
row it saves, and use it to redirect to the <code>view()</code> action.
Speaking of which...</p>

<pre class="brush: php">
&lt;?php

class Posts extends Controller
{
    /* ... */
    
    public function view($id)
    {
        $this->data['post'] = $this->model->find($id);
    }
}
</pre>
    
<p>Alrighty.</p>

<h3>New views for everybody</h3>

<p>We have to be careful in a couple of spots when writing views with forms.
This is <code class="file">/blog/app/views/posts/add.php</code>:</p>

<pre class="brush: php">
<form method="post" action="<?php url('posts/add'); ?>">
    <div>
        Title: <input type="text" name="title" id="title">
    </div>
    <div>
        <textarea name="body" id="body" cols="30" rows="10"></textarea>
    </div>
    <div>
        <input type="submit">
    </div>
</form>
</pre>
    
<p>Open <a href="http://locahost/blog/post/add">http://locahost/blog/post/add</a>
and notice this:</p>

<ul>
    <li>We use POST as method</li>
    <li>We send the form to the <code>add()</code> action</li>
    <li>We're not setting the <code>date</code> or the <code>author_id</code></li>
</ul>

<p>That last one point is dangerous. We can, potentially, save a post without
providing those fields, but then we'll be creating an incomplete set of
data. The best way to fix this is to add something to our <code>add()</code>
action, back in the controller file:</p>

<pre class="brush: php">
&lt;?php

    /* ... */
    
    public function add()
    {
        if ($this->post) {
            $this->post['date'] = time();
            $this->post['author_id'] = user()->id;
            $post = $this->model->save($this->post);
            redirect('posts/view/' . $post['id']);
        }
    }

    /* ... */
</pre>
    
<p>The strangest point here should be the call to the <code>user()</code>
function. It returns the currently-logged in user data as a
<code>stdObject</code>. We assume that particular user is the author of the
blog post.</p>

<h3>The <code>edit</code> view</h3>

<p>Very similar to the <code>add</code> view, but we're populating the
fields with the existing post data, and using some hidden-input magic
to pass data to the controller:</p>

<pre class="brush: php">
<form method="post" action="&lt;?php url('posts/edit/' . $post['id']); ?>">
    <input type="hidden" name="id" id="id" value="&lt;?php echo $post['id']; ?>">
    <input type="hidden" name="author_id" id="author_id" value="&lt;?php echo $post['author_id']; ?>">
    <div>
        Title: <input type="text" name="title" id="title" value="&lt;?php echo $post['title']; ?>">
    </div>
    <div>
        <textarea name="body" id="body" cols="30" rows="10">&lt;?php echo $post['body']; ?></textarea>
    </div>
    <div>
        <input type="submit">
    </div>
</form>
</pre>

<p>No need to include the <code>date</code> field here. <code>save()</code>
works in such a way that if it detects there's a primary key field in the
<code>$this->post</code> array, it performs an update, and it only updates
those fields present in the array.</p>

<h3>The <code>view</code> view, and fetching the post author</h3>

<p>Funny? The view file for the <code>view()</code> action is 
<code class="file">/blog/app/views/posts/view.php</code>:</p>

<pre class="brush: php">
&lt;h2>&lt;?php echo $post['title']; ?>&lt;/h2>
&lt;p>by &lt;?php echo $post['author_id']; ?> (&lt;?php echo date("Y-m-d", $post['date']); ?>)&lt;/p>
&lt;p>&lt;?php echo $post['body']; ?>&lt;/p>
</pre>

<p>You'll see that the author is printed as "<code>0</code>". It's a problem we 
can solve using the default model in a better way:</p>

<pre class="brush: php">
&lt;?php

class Posts extends Controller
{
    /* ... */
    
    public function view($id)
    {
        $this->model->add_parent('users', 'author_id');
        $this->find_related(true);
        $this->data['post'] = $this->model->find($id);
    }
}
</pre>

Then, change <code>echo $post['author_id']</code> in the view to
<code>echo $post['users']['username'];</code>, and you should see 
<em>by admin</em> below the post title.

<p>That's still sub-optimal, but works.</p>