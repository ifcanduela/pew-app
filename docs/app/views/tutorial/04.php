<div class="tutorial_navigation">
    <a href="<?php url('tutorial/03'); ?>" class="prev">&laquo; A controller</a>
    <a href="<?php url('tutorial/05'); ?>" class="next">Dealing with regret &raquo;</a>
</div>

<h2>Nice view</h2>

<p>Views are the files you'll be using to display content. There's
a lot to cover with views, but this will suffice for now.</p>

<p>Create <code class="file">blog/app/views/posts/index.php</code>:</p>

<pre class="brush: php">
&lt;?php foreach ($posts as $post): ?>
    <h1>&lt;?php echo $post['title']; ?> (&lt;?php echo $post['date']; ?>)</h1>
    <p>&lt;?php echo $post['body']; ?></p>
&lt;?php endforeach; ?>
</pre>

<p>The <code>$posts</code> variable was made available to the view
in the <code>index</code> function of the Posts controller. It
contains a numeric index for each blog post, so we loop through it
with <code>foreach</code>.</p>

<p>You have a little control over views from the Controller. You can use a
different view by setting <code>$this->view = 'other_index'</code>, switch
layouts with <code>$this->layout = 'alternate'</code> or bypass the view
altogether with <code>$this->render = false</code>.</p>

<div class="tutorial_navigation">
    <a href="<?php url('tutorial/03'); ?>" class="prev">&laquo; A controller</a>
    <a href="<?php url('tutorial/05'); ?>" class="next">Dealing with regret &raquo;</a>
</div>