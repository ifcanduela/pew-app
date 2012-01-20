<h2>Configuring the blog application</h2>

<p>Let's begin messing with the files. Open 
<code class="file">/blog/app/config/config.php</code> in your favorite
code editor and take a look at what you see.</p>

<p>First thing should be the <code>DEBUG</code> constant. It's not that
important, but it shows the developer more meaningful errors if it's set to 
1. You can try it now by trying to access any blog URL. For example, if you
go to <a href="http://localhost/blog/posts/index">http://localhost/blog/posts/index</a> (or whatever your 
development URL is), you'll see a <em>Controller missing</em> error. 
Changing <code>DEBUG</code> to 0 will transform that error into a generic 
<code>404 PAGE NOT FOUND</code> error.</p>

<p>The following constants are <code>USEDB</code>, <code>USEAUTH</code> and
<code>USESESSION</code>, and their purpose is clearer than 
<code>DEBUG</code>'s. We want all of them to be true, so change that.</p>

<p><code>APPLICATION_TITLE</code> is obvious too. I changed mine to 
<code>PewBlog</code>. The last ones are <code>DEFAULT_CONTROLLER</code> and
<code>DEFAULT_ACTION</code>, and they control which page will be shows by
default. Set <code>DEFAULT_CONTROLLER</code> to <code>'posts'</code> and
leave <code>DEFAULT_ACTION</code> as <code>'index'</code>.</p>

<p>Finally, <code>DEFAULT_LAYOUT</code> is <code>'default'</code>. That will
suffice for now, until you get into the more designy side of things.</p>

<p>There are more (hidden!) configuration settings, like one that lets you 
change the view file extension, for example, but we're not changing anything 
of that.</p>

<h3>Database configuration</h3>

<p>Open <code class="file">/blog/app/config/database_configuration.php</code>
and scroll down to the <code>$config</code> array. For our purposes, and
assuming your <code class="file">blog.db</code> database file is in the
<code class="file">/blog/app/config</code> folder, this is the appropriate
configuration:</p>

<pre class="brush: php">
&lt;?php

/* ... */

public $config = array
    (
        'default' => array
        (
            'engine' => SQLITE,
            'file' => 'app/config/blog.db'
        )
    );
</pre>

<p>The location of the database file is relative to the
<code class="php">/blog/index.php</code> file, but you can provide an
absolute path if you want.</p>

<p>For MySQL we could use something like this:</p>

<pre class="brush: php">
&lt;?php

/* ... */

public $config = array
    (
        'default' => array
        (
            'engine' => MYSQL,
            'host' => 'localhost',
            'user' => 'blog_admin',
            'pass' => 'blog_admin_password',
            'name' => 'blog'
        ),
    );
</pre>

<p>Of course, substitute your host, credentials and database name there.</p>

<p>In the next chapter, we'll be creating a user. Exciting!</p>
