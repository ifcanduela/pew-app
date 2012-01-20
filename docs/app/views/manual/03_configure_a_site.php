<h1>Configure A Site</h1>
            
<p>We're doin't it. Grab the <strong class="pewpewpew">Pew-Pew-Pew</strong>
files and drop them into your <code class="filesystem">htdocs</code> folder. You
should be staring at a folder window containing something like the
following:</p>

<ul>
    <li>C:\xampp\htdocs\my_pew\ &larr; you are here</li>
    <li>C:\xampp\htdocs\my_pew\app\</li>
    <li>C:\xampp\htdocs\my_pew\sys\</li>
    <li>C:\xampp\htdocs\my_pew\www\</li>
    <li>C:\xampp\htdocs\my_pew\.htdocs</li>
    <li>C:\xampp\htdocs\my_pew\index.php</li>
</ul>

<h2>Basic stuff</h2>

<p>You'll be changing paradigms and rewiring the way people understand the
internet in the <code class="filesystem">app</code> folder. In fact, you should
open <code class="filesystem">app/config/config.php</code> right now and change
a few values, like the <code class="constant">APPLICATION_TITLE</code> or the
<code class="constant">DEFAULT_</code> values for controller, action and layout.
Also, before you release your tip-of-the-spear product into the wild, be sure to
set <code class="constant">DEBUG</code> to <code class="php">0</code> or <code
class="php">false</code>.</p>

<h2>Databases</h2>

<p>Only MySQL is supported now. To configure the DB, open <code
class="filesystem">app/config/database_configuration.php</code> and
enter your connection settings. You should know how this goes.</p>

<h2>Your files</h2>

<p>Apart from that, you can drop stylesheet files in the <code
class="filesystem">www/css</code> folder and JavaScript files in the
<code class="filesystem">www/js</code> folder, but they can actually
be anywhere. Be sure to insert them into your fancypants layout,
though &mdash; create it in <code
class="filesystem">app/views/my_layout.layout.php</code> (or copy the default
one from <code
class="filesystem">sys/default/views/pewpewpew.layout.php</code>).</p>