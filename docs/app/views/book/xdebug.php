<h2>Installing Xdebug</h2>

<p>Xdebug is a PHP extension by Eric Rethans that improves the debugging experience in
PHP. It has become the standard thanks to its funcionality, which includes
full backtraces upon errors, script time accountability, code coverage
reporting and performance profiling. It has a lot of other little things
that make it worth it to spend five minutes installing it.</p>

<p>Installing Xdebug has become easy as of late. You go to
<a href="http://xdebug.org/find-binary.php">xdebug.org/find-binaries.php</a>
and follow the instructions. Most of the guessing work is done for you.</p>

<p>You'll need a <em>phpinfo</em> output that you can get either by calling the
phpinfo() function from a script.</p>

<pre class="brush: php">
&lt;?php phpinfo();
</pre>

<p>Or you can go the command line route. On windows and Linux:</p>

<pre>php -info > phpinfo.txt</pre>

<p>Either way, you'll end up with a document &mdash; be it a web page
or a plain-text file, that you need to copy/paste into the big textarea of
Xdebug website.</p>

<p>After that download the file, extract, copy to the specified folder and
edit you <code class="file">php.ini</code> to load Xdebug.</p>

<h3>But what's it good for?</h3>

<p>I recommend using NetBeans for serious debugging sessions. It has Xdebug
support integrated into the IDE, which enables awesome Visual Studio-like
debugging, with breakpoints, step-by-step execution and variable watches.</p>

<p>At the <em>everyday</em> level, Xdebug provides better error messages, with
full backtraces that show the call sequence that led to
the error. You can also print these traces anywhere to get a sense of the timing
in your script.</p>

<p>It can also prevent infinite loops, which is very useful if we miss
incrementing a control variable somewhere. Make sure, however, to disable Xdebug
this in public servers, since it exposes an unconfortable amount of information
about your site on every error.</p>
