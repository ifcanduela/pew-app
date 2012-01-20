<div class="tutorial_navigation">
    <a href="<?php url('tutorial/index'); ?>" class="prev">&laquo; Introduction</a>
    <a href="<?php url('tutorial/02'); ?>" class="next">Tables and tables of data &raquo;</a>
</div>

<h2>Setting things app</h2>

<p>We'll try to be original and start from the beginning. Let's go with
the development environment.</p>

<p>I'll take for granted that you have a working Apache/PHP
installation, and you know the location of Apache's
<code class="file">htdocs</code> folder.</p>

<div class="note">    
    <p>If you need a pointer to which tools to use, I can help.
    <ul>
        <li>Check out <a href="http://apachefriends.org/xampp">Xampp</a> for
        an easy way to have Apache, PHP and MySQL setup.</p></li>
        <li>You're probably going to use a text editor. You can use Windows
        Notepad, but it's not really the best tool for the job. Consider
        installing flo's <a href="http://www.flos-freeware.ch/notepad2.html">Notepad2</a>
        or ActiveState's <a href="htt://www.activestate.com/komodo-edit">Komodo Edit</a>.</li>
        <li>Look into <a href="http://www.netbeans.org/">NetBeans</a> if you're
        used to Visual Studio or Eclipse.</li>
        <li>I use the xdebug extension for debuggin PHP iside NetBeans. There's
        a very handy installation assistant
        <a href="http://xdebug.org/find-binary.php">over the Xdebug
        website</a>.</li>
    </ul>
</div>

<p>Create a new folder inside <code class="file">htdocs</code> called <code class="file">blog</code>. We'll be
using it exclusively from now on.</p>

<h3>Let's setup</h3>

<p>Now you'll want to get Pew-Pew-Pew, which comes in a handy ZIP
file.</p>

<p>Open <code class="file">pew-pew-pew.zip</code> with any archiving program (Windows Explorer
can do it) and drag-and-drop the contents into the <code class="file">blog</code> folder you
created.</p>

<p>Make sure you have a folder structure similar to this one:</p>

<ul>
    <li>htdocs
        <ul>
            <li>blog</li>
            <ul>
                <li>app</li>
                <li>sys</li>
                <li>www</li>
                <li>.htaccess</li>
                <li>index.php</li>
            </ul>
        </ul>
    </li>
</ul>

<p>We have to set up some application data. Mostly, the default
values are enough, but I'll try to explain what these settings do.
</p>

<p>Open <code class="file">blog/app/config.php</code>, then find the following
lines and set the values for these constants:</p>

<pre class="brush: php">
&lt;?php

define('USEDB', 'default');
define('USEAUTH', false);
define('USESESSION', false);

define('DEFAULT_CONTROLLER', 'posts');
define('DEFAULT_ACTION', 'index');
</pre>
            
<p>We'll be using the database connection called <code>default</code>, which
will be configured soon. Also, for the moment there's no need for
authentication and sessions.</p>

<p>When saying that the default controller/action pair is posts/index, we're
actually telling the app what to use when there's not enough information coming
from the URL.</p>

<div class="tutorial_navigation">
    <a href="<?php url('tutorial/index'); ?>" class="prev">&laquo; Introduction</a>
    <a href="<?php url('tutorial/02'); ?>" class="next">Tables and tables of data &raquo;</a>
</div>
