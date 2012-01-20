<div class="tutorial_navigation">
    <a href="<?php url('tutorial/05'); ?>" class="prev">&laquo; Dealing with regret</a>
    <a href="<?php url('tutorial/07'); ?>" class="next">Cheap manpower &raquo;</a>
</div>

<h2>All of it</h2>

<p>We're able to list, add and edit posts, but we still have to
modify the index view and add some buttons for these functions:</p>

<pre class="brush: php html">
&lt;div>&lt;a href="&lt;?php url('posts/add'); ?>">Add post&lt;/a>&lt;/div>

&lt;?php foreach ($posts as $post): ?>
    <h1>&lt;?php echo $post['title']; ?>
      (&lt;?php echo $post['date']; ?>)</h1>
    &lt;div>
        &lt;a href="&lt;?php url('posts/edit/' . $post['id']); ?>">Edit
          post&lt;/a>
    &lt;/div>
    <p>&lt;?php echo $post['body']; ?></p>
&lt;?php endforeach; ?>
</pre>

<p>Okay, that should do it. The blog is somewhat functional. I still think you
should take the default layout and add you own touch, but that's a web design
thing and there are <a href="http://designinstruct.com/">thousands</a> of
<a href="http://webdesign.tutsplus.com/">websites</a> that
<a href="http://smashingmagazine.com/">deal</a> with
<a href="http://webdesignledger.com/">that</a>. Or
<a href="http://designshack.co.uk/">more</a>.</p>

<p>That could be it, but I'll try to press forward. The next chapters will
show you how to use a couple of default controllers, the <em>Pages</em> controller and
the <em>Users</em> controller. Things are starting to get complicated, tighten
your belts.</p>

<div class="tutorial_navigation">
    <a href="<?php url('tutorial/05'); ?>" class="prev">&laquo; Dealing with regret</a>
    <a href="<?php url('tutorial/07'); ?>" class="next">Cheap manpower &raquo;</a>
</div>
