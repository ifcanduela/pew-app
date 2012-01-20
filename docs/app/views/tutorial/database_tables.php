<h2>Tables and rows</h2>

<p>We have to think about our data layout for the blog. I've come up with a 
simple structure that will allow us to log in, add, edit and view posts, 
register additional users with different provileges, add and view comments for
posts, and generally have a small, functional jounal-like app in about an 
hour. It's not Wordpress, but hey &mdash; it's <em>handcrafted</em>.</p>

<p>I'm using SQLite because itI find it very convenient for a small website.
It also simplifies moving around the app from server to server, or from 
computer to computer (I switch computers and operating systems a lot).</p>

<p>So here you are, this is for <strong>SQLite</strong>:</p>

<pre class="brush: sql">
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT,
    password TEXT,
    email TEXT,
    role INTEGER
);

CREATE TABLE posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT,
    date INTEGER,
    body TEXT,
    author_id INTEGER
);

CREATE TABLE comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    date INTEGER,
    body TEXT,
    user_id INTEGER,
    post_id INTEGER
);
</pre>

<p>Here is the <strong>MySQL</strong> code:</p>

<pre class="brush: php">
CREATE TABLE users (
    id INT(10) NOT NULL AUTO_INCREMENT,
    username VARCHAR(255),
    password VARCHAR(255),
    email VARCHAR(255),
    role INT(10),
	PRIMARY KEY (`id`)
);

CREATE TABLE posts (
    id INT(10) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255),
    date INT(10),
    body TEXT,
    author_id INT(10),
	PRIMARY KEY (`id`)
);
 
CREATE TABLE comments (
    id INT(10) NOT NULL AUTO_INCREMENT,
    date INTEGER,
    body TEXT,
    user_id INT(10),
    post_id INT(10),
	PRIMARY KEY (`id`)
);
</pre>

<p>Dates will be stored as integer timestamps, and the password field will
be 40 characters long because that's the length of a resulting md5 hash.
Also, note that I'm adding <code>AUTOINCREMENT</code> to the primary key 
definitions &mdash; that will avoid the reuse of identifiers by SQLite.</p>

<p>You should go ahead and create the database file. I've used the simple
<a href="https://addons.mozilla.org/en-US/firefox/addon/sqlite-manager/">SQLite manager</a> 
Firefox extension with success, and there's also 
<a href="http://www.yunqa.de/delphi/doku.php/products/sqlitespy/index">SQLiteSpy</a> 
for Windows, which works really well.</p>

<p>I usually put the database files in the <code class="file">/app/config</code>
folder, but it can be anywhere, and it's probably safer to put them outside
the htdocs folder. For this tutorial, our database will be found at
<code class="file">htdocs/blog/app/config/blog.db</code>.</p>

<p>To conclude this section, I have to mention that the framework has a
SQLite bootstrapping mechanism. If you declare a sqlite_init() function in
the <code class="file">app/config/bootstrap.php</code> file, it will be
called whenever the database file does not exist or is empty. You can place
your <code>CREATE</code> or <code>INSERT</code> statements there.</p>
    