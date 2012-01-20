<div class="tutorial_navigation">
    <a href="<?php url('tutorial/04'); ?>" class="prev">&laquo; Nice view</a>
    <a href="<?php url('tutorial/06'); ?>" class="next">All of it &raquo;</a>
</div>

<h2>Dealing with regret</h2>

<p>So you're thinking that single post we have is not <em>personal</em> enough.
We'll be adding new posts soon, and it's quite easy, but editing posts can be
convoluted and we'll face the pain first:</p>

<pre class="brush: php">
&lt;?php
public function edit($post_id)
{
    if (isset($this->parameters['form'])) {
        $this->model->save($this->parameters['form']);
        redirect('posts/index');
    }
    
    $this->data['post'] = $this->model->find($post_id);
}
</pre>

<p>Yeah, the model handles it. Remember we didn't create a model. So
<em>yeah</em>.</p>

<p>We're receiving a <code>$post_id</code> through the URL, presumably telling
us which post we're handling. The first thing to do is checking whether or not
the Edit Post form was submitted. If it was, we have the new post info, so we
save it and redirect to the posts index.</p>

<p>If the form was not submitted, we fetch its data using this parameters and
display it in a form. We'll need a view for that, create
<code class="file">blog/app/views/posts/edit.php</code> and type this:</p>

<pre class="brush: xml">
&lt;h1>Edit post&lt;/h1>
&lt;form method="POST" action="&lt;?php
  url('posts/edit/' . $post['id']); ?>">
    &lt;input type="hidden" name="id" id="id"
      value="&lt;?php echo $post['id']; ?>">
    &lt;p>Title: &lt;input name="title" id="title"
      value="&lt;?php echo $post['title']; ?>">&lt;/p>
    &lt;textarea name="body" id="body">&lt;?php echo $post['body']; ?>
    &lt;/textarea>
    &lt;p>&lt;input type="submit"> or
      &lt;a href="&lt;?php url('posts/index'); ?>">Cancel&lt;/a>
    &lt;/p>
&lt;/form>
</pre>

<p>Not overwhelming, I hope. Submitting this will make the <code>if
(isset($this->parameters['form']))</code> line in the controller to be
<code>true</code>, because the form data is <em>POST</em>ed.</p>

<p>Notice how we are passing the post id to the action: as a URL
segment after the controller and action segments, and in a hidden
input field in the form. The first one is more appropriate for links,
and the second one is useful to simplify the Model::save() call in
the controller.</p>

<h3>Tell me more about that</h3>

<p>Listing blog posts is only fun if you have more than one post, so
let's implement an <code>add</code> action in the Posts controller:
</p>

<p>Open
<code class="file">blog/app/controllers/posts.class.php</code>, and
add a new method to the Posts class:</p>

<pre class="brush: php">
&lt;?php
class Posts extends Controller
{
    /* index() */

    public function add()
    {
        if (isset($this->parameters['form'])) {
            $new_post = $this->parameters['form'];
            $new_post['date'] = date('Y-m-d H:i:s');
            $this->model->save($new_post);
            redirect('posts/index');
        }
    }
}
</pre>

<p>Very similar to the <code>edit</code> action. This may be confusing. This
action does nothing (that is, it goes directly to the view) if there isn't
<em>POST</em> data submitted by a form. In the case the <code>'form'</code>
parameter is set, we can get all the form fields and automatically create a
post. Add the current date and time and use the model to save the post.
Afterwards, just redirect to the posts index.</p>

<p>The Add Post view is very simple, too. It contains a form that
POSTs data to our Posts::add() method. If the input names are the
same as the table column names, the controller action will be
simplified.</p>

<p>Create app/views/posts/add.php</p>

<pre class="brush: xml">
&lt;h1>New post&lt;/h1>
&lt;form method="POST" action="&lt;?php url('posts/add'); ?>">
    &lt;p>Title: &lt;input name="title" id="title">&lt;/p>
    &lt;textarea name="body" id="body">&lt;/textarea>
    &lt;p>&lt;input type="submit"> or
      &lt;a href="&lt;?php url('posts/index'); ?>">Cancel&lt;/a>&lt;/p>
&lt;/form>
</pre>

<div class="tutorial_navigation">
    <a href="<?php url('tutorial/04'); ?>" class="prev">&laquo; Nice view</a>
    <a href="<?php url('tutorial/06'); ?>" class="next">All of it &raquo;</a>
</div>