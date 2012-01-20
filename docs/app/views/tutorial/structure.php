<h2>Application structure</h2>

<p>One thing I like about using a framework is that all the application
files end up in the right places, making it a tidy package. Pew-Pew-Pew is
no different.</p>

<p>You'll be rewiring the way people think about the internet and changing 
paradigms in the <code>/blog/app</code> folder, mostly. There, you'll create
controller classes and model classes, and view files. Controllers are
the backbone of the website, since they contain the actions your application
can perform. We'll create controllers for users, posts, comments, each with
action like index, view, edit or login. Models, on their part, are ways to
access the database contents. They're not really <em>models</em> in the 
way other libraries use the word. I just thought <em>database-accessing
objects</em> didn't have the same ring about it.</p>

<p>The <code class="file">/app/views</code> folder contains both layouts,
which work as general page styles, and views, that are the files that
format and display the data the controllers generate.</p>

<h3>Assets</h3>

<p>On the other hand, the stylesheets, JavaScript scripts, image files
and all the rest of assets are in the <code class="file">/blog/www</code> 
folder.</p>

<p>There's a structure already there, with <code class="file">css</code>, 
<code class="file">js</code>, and <code class="file">img</code> subfolders,
but anything can go anywhere.</p>

<h3>The <code>sys</code> folder</h3>

<p>The <code class="file">/blog/sys</code> folder contains the library 
files. Generally it should be ignored, but maybe that's difficult when it's
standing there, between your <code class="file">/app</code> and 
<code class="file">/www</code> folders. We can move it away.</p>

<p>I usually share a <code class="file">/sys</code> folder between all my 
projects, and that's handy. Just put it anywhere you want and change the
<em>framework configuration</em> setting in <code class="file">/blog/index.php</code>.</p>
    
<pre class="brush: php">
&lt;?php

# protection against direct access to scripts
define('PEWPEWPEW', true);

# framework configuration
require 'sys/config.php';     # <===== CHANGE THIS

# some benchmarking
$t = get_execution_time();

try {
    # ...and the magic begins!
    Pew::Get('App')->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
</pre>

<p>Take a look at my usual setup:</p>

<pre>
htdocs
 |   abandoned_app
 |   forgotten_project
 |   sys
 |   unfinished_website
    </pre>
    
<p>Those three projects share a <code class="file">sys</code> folder by 
changing their <em>framework configuration</em> to:</p>

<pre class="brush: php">
&lt;?php

/* ... */

# framework configuration
require '../sys/config.php';
</pre>
