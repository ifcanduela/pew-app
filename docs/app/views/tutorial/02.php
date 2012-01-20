<div class="tutorial_navigation">
    <a href="<?php url('tutorial/01'); ?>" class="prev">&laquo; Setting things app</a>
    <a href="<?php url('tutorial/03'); ?>" class="next">A controller &raquo;</a>
</div>

<h2>Tables and tables of data</h2>

<p>Open
<code class="file">blog/app/config/database_configuration.php</code> now
and configure an SQLite database:</p>

<pre class="brush: php">
<ul>
&lt;?php
protected $config = array
(
    'default' => array
    (
        'engine' => SQLITE,
        'file' => 'app\config\db\blog.db'
    ),
    
    /* ... */
);
</pre>

<p>That defines the <code>default</code> database configuration
that we specified in <code class="file">config.php</code>. It will
read from and write to a file called blog.db. But first, that file
should be created. Keep reading for that.</p>

<h3>Bootstrap the database</h3>

<p>There's a way to make sure things are there when you need 'em,
and it's called bootstrapping. The
<code class="file">bootstrap.php</code> file can contain function
definitions and constant declarations, and there's also a
couple of hooks that we can implement here.</p>

<p>The one we'll be taking on now is the <code>sqlite_init</code>
function, which is called whenever the database file does not exist
or is empty (meaning its size is zero bytes).</p>

<p>Open <code class="file">blog/app/config/bootstrap.php</code>, and add this
function:</p>

<pre class="brush: php">
&lt;?php
function sqlite_init(PDO $db)
{
    ob_start();
    
    $db->exec("CREATE TABLE posts (id INTEGER PRIMARY KEY, title TEXT, body INTEGER, date DATETIME)");
        
    if ($db->errorCode() !== '00000') {
        pr($db->errorInfo());
        trigger_error("Table notes: CREATE execution error");
    }
    
    $db->exec("INSERT INTO posts (title, body, date) VALUES ('First Post!', 'This is my first post', '2011-09-22 13:30:00')");
    
    if ($db->errorCode() !== '00000') {
        pr($db->errorInfo());
        trigger_error("Table notes: INSERT execution error");
    }
    
    $bootstrap_log = ob_get_contents();
    ob_end_clean();
    file_put_contents('bootstrap.log.txt', $bootstrap_log);
    
    return true;
}
</pre>

<p>The <code class="file">sqlite_init</code> function receives the PDO database
connection already set up, so you can execute commands to create tables and
insert rows. It's useful for quick deployments.</p>

<div class="tutorial_navigation">
    <a href="<?php url('tutorial/01'); ?>" class="prev">&laquo; Setting things app</a>
    <a href="<?php url('tutorial/03'); ?>" class="next">A controller &raquo;</a>
</div>
