<div class="tutorial_navigation">
    <a href="<?php url('tutorial/06'); ?>" class="prev">&laquo; All of it</a>
    <a href="<?php url('tutorial/08'); ?>" class="next">About you &raquo;</a>
</div>

<h2>Cheap manpower</h2>

<p>Access to the Edit and Add pages in not restricted, but it should. We'll be
implementing a nice login system in this section. But first we have to sort
through one smallish problem: adding a users table to the database.</p>

<p>Yeah, with SQLite that's not easy. My advice is to use something like
<a href="http://www.yunqa.de/delphi/doku.php/products/sqlitespy/index">SqliteSpy</a>
to tun queries against the blog.db file. I'll give you the SQL so you can add
a <code>users</code> table:</p>

<pre class="brush: sql">CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    username TEXT,
    password TEXT,
    level INTEGER
);</pre>

<p>And add a couple of users:</p>

<pre class="brush: sql">INSERT INTO users (username, password, level)
   VALUES ('admin', 'admin', 0);
INSERT INTO users (username, password, level)
   VALUES ('editor', 'editor', 1);</pre>

<p>I'm not too sure if SQLite allows inserting several rows with a single
statement, so two statements for us. We want the admin to be able to add, edit
and delete posts, and the editor to only edit them.</p>

<p>Next, update the <code class="file">blog/app/config/config.php</code> file
to reflect that we want to use authentication and session management:</p>

<pre class="brush: php">
define('USEAUTH', true);
define('USESESSION', true);
</pre>

<h3>Identify yourself</h3>

<p>The Users controller is ready to go. It just needs a <code>users</code> table
with  <code>username</code> and <code>password</code> fields. You can copy
<code class="file">users.class.php</code> to your app's
<code class="file">controllers</code> folder and modify it, it you wanted.</p>

<p>The login view is also ready, in the
<code class="file">sys/default/views/users</code> folder. Copy
<code class="file">login.php</code> to
<code class="file">blog/app/views/users</code> or create your own:</p>

<p>This view and the Auth and Users controllers use the Session library to
store volatile application data, such as incorrect login messages or the
controller and action the user tried to see before being redirected to the
login form.</p>

<div class="tutorial_navigation">
    <a href="<?php url('tutorial/06'); ?>" class="prev">&laquo; All of it</a>
    <a href="<?php url('tutorial/08'); ?>" class="next">About you &raquo;</a>
</div>