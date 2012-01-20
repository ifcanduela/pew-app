<h2>Installation</h2>

<p>The first step is downloading the framework files. Currently, that's
done from <a href="#downloading" onclick="alert('Working on it!'); return false;">here</a>.</p>

<p>Installation is very easy: just unpack the downloaded ZIP file to a folder
inside your servers document root. In Linux, that normally is
<code class="file">/var/www</code>. In Windows it depends on how you installed
Apache, but it's normally called <code class="file">htdocs</code> and is
either <code class="file">C:\Program Files\Apache\htdocs</code> folder
or somewhere like <code class="file">C:\XAMPPP\htdocs</code> (but you should
know better than me).</p>

<p>An example directory structure is this:</p>

<p><img src="<?php www('img/book_installation_1.png'); ?>" alt="Folder tree" /></p>

<p>Surely, you may want to rename the <code class="file">pew-pew-pew</code> folder,
or even remove it altogheter and place its contents directly in the document root.</p>

<h3>Securing you files and multiple applications</h3>

<p>There are many reasons not to have the <code class="file">sys</code> folder
inside the document root, and fortunately it's easy to configure an application
to look elsewhere for the framework files.</p>

<p>Open the <code class="file">index.php</code> file and look for this line:</p>

<pre class="brush: php">
&lt;?php

/* ... */

require('sys/config.php);

/* ... */
</pre>

<p>Place your <code class="file">sys</code> folder anywhere you want and update that
<code>require()</code> statement accordingly. The path is always relative to the
<code class="file">index.php</code> file.</p>

<p>This feature also enables multiple applications to share a single
<code class="file">sys</code> folder, but be aware that each application must
be in its own directory and have its own <code class="file">index.php</code>
file. The documentation distributed alongside the framework is an example
of multi-app installation, since it's actually a separate website inside the
pew-pew-pew folder.</p>
