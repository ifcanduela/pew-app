<h2>Updating things</h2>

<p>This chapter is going to be a little more <em>do it yourself</em> than any
previous one, since we're making many changes to a few files.</p>

<p>You may have noticed that I have not addded links anywhere. That's something 
you should do now. Since the URLs are a little different that the usual 
query string, <span class="pewpewpew">Pew-Pew-Pew</span> provides a two
functions that make linking to pages and files easy, <code>url()</code> and 
<code>www()</code>. Both print a URL to the base application address, with 
the addition of the assets folder (typically <code class="file">www</code>) in
the case of <code>www()</code>.

<p>Armed with that knowledge, we could add a link to the 
<code class="file">index.php</code> this way.</p>

<pre class="brush: php">
<p><a href="&lt;?php url('posts/add') ?>">add a new post</a></p>
&lt;?php if ($posts) foreach ($posts as $post): ?>
    <h1>&lt;?php echo $post['title']; ?></h1>
    <p>&lt;?php echo date("d-m-Y", $post['date']); ?>
    [<a href="&lt;?php url('posts/edit/' . $post['id']); ?>">edit</a>]</p>
    <div>&lt;?php echo $post['body']; ?></div>
&lt;?php endforeach; ?>
</pre>

<p>But we want to protect the add and edit buttons from normal users, right? 
Wrap it around a user-role check:</p>

<pre class="brush: php">
&lt;?php if (user() and user()->role === '0'): ?>
    <!-- whatevers -->
&lt;?php endif; ?>
</pre>

<p>It might seem weird, but results returned from SQlite databases are always 
<em>stringified</em>.</p>

<p>Also, try adding the following line as the first thing in the 
<code>add()</code> and <code>edit()</code> actions.</p>

<pre class="brush: php">
$this->require_auth = true;
</pre>

<p>It will redirect anonymous users to the login page in the chance they try to 
open the add or edit form pages by typing the URL.</p>

<aside>Please remember that the Auth library will be updated and expanded 
in future versions of the framework. This way of doing things is provisional.
</aside>

<p>Next up, everything dealing with comments.</p>