<h2>Displaying your posts</h2>

<p>Which posts? We have none. Go now and insert some using your trusty
SQLite front-end:</p>
    
<pre class="brush: sql">
INSERT INTO posts (title, body, date, author_id)
VALUES ('First', 'The first post', 1322224257, 1),
       ('Second', 'The second post', 1322225257, 1),
       ('Third', 'The third post', 1322226257, 1);</pre>
    
<p>You can get the current timestamp <a href="http://www.epochconverter.com/">here</a>.</p>
    
<p>Now that we have a populated table, let's create a new file. The view
for the <code>index()</code> action will be
<code class="file">/blog/app/views/posts/index.php</code>. Create that and
start editing:</p>
    
<pre class="brush: php">
&lt;?php if ($posts) foreach ($posts as $post): ?>
    <h1>&lt;?php echo $post['title']; ?></h1>
    <p>&lt;?php echo date("d-m-Y", $post['date']); ?></p>
    <div>&lt;?php echo $post['body']; ?></div>
&lt;?php endforeach; ?>
</pre>
    
<p>Many problems there, but it works. We probably should display the most
recent one first. We'll leave that to the model when we get there. For the
moment, this is enough.</p>

<p>Besides that, our current to-do list is as follows:</p>

<ul>
    <li>Add an admin section to create, edit and delete posts</li>
    <li>Allow visitors to create accounts</li>
    <li>Allow members to submit comments</li>
</ul>

<p>Let's improve out Posts controller with some new actions.</p>
