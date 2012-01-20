<h2>Adding comments</h2>

<p>This is going to be a little convoluted, because I'd like to have it up and
running quickly. There are a few ways of providing this functionality, and I've
chosen the simplest.</p>

<ul>
    <li>Only logged-in users are able to submit comments.</li>
    <li>Comments and the comments form are only displayed in the 
    <code>posts/view/$id</code> view.</li>
    <li>We're not implemeting an approval process or any kind of filtering.</li>
</ul>

<h3>The Comments controller</h3>

<p>Create <code class="file">\blog\app\controllers\comments.class.php</code>
and open it for editing. Type this:</p>

<pre class="brush: php">
&lt;?php

class Comments extends Controller
{
    function submit($post_id)
    {
        if ($this->post) {
            $this->post['date'] = time();
            $this->model->save($this->post);
        }

        redirect('posts/view/' . $post_id);
    }
}
</pre>

<p>Simple and effective.</p>

<h3>At last, a model</h3>

<p>We are going to automatically fetch comments in the 
Posts controller's <code>view()</code> action, so no reason to include the
standard <action>index()</action> here. However, I'd like to introduce 
something new here, so we're creating the <em>Posts model</em>. It will
be in the file <code class="file">\blog\app\models\posts_model.class.php</code>:</p>

<pre class="brush: php">
&lt;?php

class PostsModel extends Model
{
    public $has_many = array('comments' => 'post_id');
    public $belongs_to = array('users' => 'author_id');

    public $order_by = 'date DESC';
}
</pre>

<p>We define the <code>$has_many</code> and <code>$belongs_to</code> properties
as arrays with <code>table -> foreign key</code> pairs. If we tell the model
to query related models before performin a <code>find*()</code> operation, it
will return parent and children rows from other tables. Very convenient.</p>

<p>We've also told the model to use a default sorting order for the database 
queries &mdash; the posts will now be displayed in reverse chronological
order, like is common in blogs.</p>

<p>Create a <code>CommentsModel</code> class too in file 
<code class="file">\blog\app\models\comments_model.class.php</code>:</p>

<pre class="brush: php">
&lt;?php

class CommentsModel extends Model
{
    public $belongs_to = array('users' => 'user_id', 'posts' => 'post_id');
}
</pre>

<p>Change the <code>view()</code> action of the <strong>Posts</strong> controller
like this:</p>

<pre class="brush: php">
&lt;?php

class Posts extends Controller
{
    /* ... */
    
    public function view($id)
    {
        $this->model->find_related(true);
        $this->model->comments->find_related(true);
        $this->data['post'] = $this->model->find($id);
    }
}
</pre>

<p>As we saw in the previous chapter, the author fields will be available in the 
<code>'users'</code> index of the <code>$post</code> array. The 
<code>'comments'</code> index will hold another array, with all the comments
of the post. Each comment will have an index for its author in the same way the 
posts do.</p>

<h3>The <em>comments</em> form</h3>

<p>We have to add a form to the <code class="file">\posts\view.php</code> view 
file. And now we've opened that file again, we're also inserting the comments
for the post. Following with the simple, half-baked tradition of this tutorial, 
type  this into the file:</p>

<pre class="brush: php">
&lt;h2>&lt;?php echo $post['title']; ?>&lt;/h2>
&lt;p>by &lt;?php echo $post['author_id']; ?> (&lt;?php echo date("Y-m-d", $post['date']); ?>)&lt;/p>
&lt;p>&lt;?php echo $post['body']; ?>&lt;/p>

&lt;?php if ($post['comments']): ?>
    &lt;?php foreach ($post['comments'] as $comment):
    <div>
        <h4>&lt;?php echo $comment['users']['username']; ?> said:</h4>
        <p>&lt;?php echo $comment['body']; ?></p>
    </div>
    &lt;?php endforeach; ?>
&lt;?php else: ?>
    <p>No comments!</p>
&lt;?php endif; ?>

&lt;?php if (user()): ?>
<form action="$lt;?php url('comments/submit/' . $post['id']); ?>" method="POST">
    <input type="hidden" name="post_id" id="post_id" value="&lt;?php echo $post['id']; ?>">
    <input type="hidden" name="user_id" id="user_id" value="&lt;?php echo user()->id; ?>">
    <div>
        <textarea name="body" id="body"></textarea>
    </div>
    <div>
        <input type="submit">
    </div>
</form>
&lt;?php endif; ?>
</pre>

<p>That does the trick, I think. However, nobody can comment because there are 
no accounts beside yours. Let's make registration happen.</p>