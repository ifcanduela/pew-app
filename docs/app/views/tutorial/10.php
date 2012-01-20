<div class="tutorial_navigation">
    <a href="<?php url('tutorial/09'); ?>" class="prev">&laquo; Modeling the app</a>
    <a href="<?php url('tutorial/11'); ?>" class="next">That's it (mostly) &raquo;</a>
</div>

<h2>What do you think about [topic]?</h2>

<p>A blog without comments is like a movie without vampires, so let's create
a Comments controller with a couple of simple actions.</p>

<pre class="brush: php">
&lt;?php

class Comments extends Controller
{
    function index($post_id)
    {
        $this->data['comments'] =
            $this->model->find_all(compact($post_id));
    }
    
    function add($post_id)
    {
        if (isset($this->parameters['form']))
        {
            $this->model->save($this->parameters['form']);
            redirect('posts/view/' . $post_id);
        }
    }
}
</pre>

<p>This is different than adding blog posts, since the Add Comment form
will be in the View Post view. Which, by the way, we need to write.</p>

<h3>Posts in full-screen</h3>

<pre class="brush: php">
&lt;?php

class Posts extends Controller
{
    /* ... */
    
    function view($id)
    {
        $this->model->find_related(true);
        $this->data['post'] = $this->model->find($id);
    }
}
</pre>

<p>That will be enough. The <code>$post</code> variable we pass to the
view will have the post info and all the comments. This is the view:</p>

<pre class="brush: php xml">
&lt;h2 class="post-title">&lt;?php echo $post['title']; ?>&lt;/h2>

&lt;div class="post-body">
    &lt;?php echo $post['body']; ?>?>
&lt;/div>

&lt;?php if (is_array($posts['comments'])): ?>
&lt;div class="comments">
    &lt;h2>Comments&lt;/h2>
    &lt;?php foreach ($post['comments'] as $comment): ?>
    &lt;div class="comment">
        &lt;h3 class="comment-author">&lt;?php echo $comment['author']; ?>&lt;/h3>
        &lt;p class="comment-message">&lt;?php echo $comment['message']; ?>&lt;/p>
    &lt;/div> &lt;!-- comment -->
    &lt;?php endforeach; ?>
&lt;/div>
&lt;?php endif; ?>

&lt;form method="POST"
  action="&lt;?php url('comments/add/' . $post['id']); ?>">
    &lt;input type="hidden" name="post_id"
      value="&lt;?php echo $post['id']; ?>>
    Name: &lt;input type="text" name="author" id="author">&lt;br>
    &lt;textarea name="message" id="message" cols="30" rows="10">
    &lt;/textarea>&lt;br>
    &lt;input type="submit">
&lt;/form>
</pre>

<p>Well, that looks complicated. The view has three different sections:</p>

<ol>
    <li>The post information</li>
    <li>The comments list</li>
    <li>The new comment form</li>
</ol>

<p>Our new form will send its data to the Add Comment action, which will save
the comment and return us here.</p>

<div class="tutorial_navigation">
    <a href="<?php url('tutorial/09'); ?>" class="prev">&laquo; Modeling the app</a>
    <a href="<?php url('tutorial/11'); ?>" class="next">That's it (mostly) &raquo;</a>
</div>
