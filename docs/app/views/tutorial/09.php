<div class="tutorial_navigation">
    <a href="<?php url('tutorial/08'); ?>" class="prev">&laquo; About you</a>
    <a href="<?php url('tutorial/10'); ?>" class="next">What do you think about [topic]? &raquo;</a>
</div>

<h2>Modeling the app</h2>

<p>We'll prepare our things for the incoming avalanche of comments to our
blog posts. In the next chapters, we have to create a Comments controller, write
models for posts and comments, add a View Post action to the Posts controller
(and its view), and create an Add action for Comments. Fun stuff.</p>

<h3>But first...</h3>

<p>Add a <code>comments</code> table to the database. Use this SQL:</p>

<pre class="brush: sql">
CREATE TABLE comments (
    id INTEGER PRIMARY KEY,
    message TEXT,
    author TEXT,
    post_id INTEGER
);
</pre>

<h3>The Posts and Comments model classes</h3>

<p>Create <code class="file">blog/app/models/posts_model.class.php</code> and
<code class="php">blog/app/models/posts_model.class.php</code>. It's easy,
people!</p>

<pre class="brush: php">
&lt;?php

class PostsModel extends Model
{
    public $has_many = array('comments' => 'post_id');
}
</pre>

<p>And..</p>

<pre class="brush: php">
&lt;?php

class CommentsModel extends Model
{
    public $belongs_to = array('posts' => 'post_id');
}
</pre>

<div class="note">
    <p>A little explanation for those interested: Without the <code>$has_many</code>
    and <code>$belongs_to</code> declarations, these model would be very much like
    the default, automatically-created models. But those declarations allow us to
    fetch comments when fetching posts, and vice-versa. Average handyness.</p>
</div>

<p>Now, you can do this to get much more for each post:</p>

<pre class="brush: php">
    $this->model->find_related(true);
    $posts = $this->model->find($post_id);
</pre>

<p>The <code>find_related</code> method only works for the next
<code>find</code>, <code>find_all</code>, <code>find_by_*</code> or
<code>find_all_by_*</code> operation though. It defaults to <em>not</em>
querying related tables.</p>

<div class="tutorial_navigation">
    <a href="<?php url('tutorial/08'); ?>" class="prev">&laquo; About you</a>
    <a href="<?php url('tutorial/10'); ?>" class="next">What do you think about [topic]? &raquo;</a>
</div>