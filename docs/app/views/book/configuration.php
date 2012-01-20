<h2>Configuration options</h2>

<p><span class="pewpewpew">Pew-Pew-Pew</span> is moderately flexible, and
comes with a few configuration options.</p>

<h3>Debug</h3>

<p>The <code>DEBUG</code> constant indicates whether to show some errors in the
browser or not. The following values are allowed:</p>

<ul>
    <li><code>define('DEBUG', 0)</code>: No debug output will be shown. Controller,
    action, view and layout errors will be treated as <code>404 PAGE NOT FOUND</code>
    errors. Model and library errors will be ignored (standard PHP error output
    will be displayed).</li>
    <li><code>define('DEBUG', 1)</code>: Verbose framework errors are displayed. The
    debug log will be printed if the corresponding element is called. This setting
    does not work for remote request, which means only visitors from the same IP
    address than the server will see the debug information.
    <li><code>define('DEBUG', 1)</code>: The same as <code>1</code>, but the log and
    errors will be shown to any visitor, regardless of their IP address.</li>
</ul>

<h3>Application title</h3>

<p>Set <code>APPLICATION_TITLE</code> to the name of your application. It will
be used by default in the <code>Controller::$title</code> property, available
inside the layout and views. This app's name, for example, is <em>Pew-Pew-Pew
documentation</em>, as displayed in the browser's title bar or tab.</p>

<h3>Default controller, action and layout</h3>

<p>These three constants (<code>DEFAULT_CONTROLLER</code>, <code>DEFAULT_ACTION</code>
and <code>DEFAULT_LAYOUT</code>) are used by the framework to decide what to do in
the circumstance that no enough data is provided via the URL or the controller.</p>

<p>The default action is only applicable to the default controller &mdash; if a
controller other than the default is requested without an action, it will always try
to call the <code>index()</code> action.</p>

<p>The default layout must reside in the <code class="file">/app/views</code> folder;
that is, if you set <code>DEFAULT_LAYOUT</code> to <code>'docs'</code>, there must be
a <code class="file">/app/views/docs.layout.php</code> file. The only exception is the
<code>'default'</code> layout, which is in
<code class="file">/sys/default/views/default.layout.php</code>. This is a barebones
layout that you can copy to <code class="file">/app/views</code> and modify. If
there's a <code>default.layout.php</code> file in <code class="file">/app/views</code>,
it will override the one in <code class="file">/sys/default/views</code>.</p>

<h3>Response formats</h3>

<p>You can <code>define('DEFAULT_RESPONSE_FORMAT', RESPONSE_FORMAT_JSON)</code> to have all your
actions return data in <abbr title="JavaScript Object NOtation">JSON</abbr> format
(or <code>RESPONSE_FORMAT_XML</code> for <abbr title="Extensible Markup Language">XML</abbr>)
if you want. This setting is somewhat <em>hidden</em> because there are very few reasons
to change it. One of them is when, for example, you want to create a web service
instead of a web site.</p>

<p>The default value is <code>RESPONSE_FORMAT_HTML</code>.</p>

<h3>Database setup</h3>

<p>Database configuration is a complicated affair. In <code class="file">/app/config/config.php</code>
you can <code>define('USEDB', true)</code> to use the default database configuration or
<code>define('USEDB', false)</code> to disable database use application-wide. However, there's more
to this setting.</p>

<p>In <code class="file">/app/config/database_configuration.php</code> there's the
<code>$config</code> array. You can define new indexes for multiple database configurations.
This is useful to quickly change databases between development, testing and release
configurations. You can set <code>USEDB</code> to a string matching one of the
<code>$config</code> array indexes to use that specific database configuration.</p>

<p>Besides that, there's the <code>DATABASE_CONFIGURATION</code> constant, that points
to the file in which the <strong>DatabaseConfiguration</strong> class is defined. This is
provided as a <em>hidden</em> setting if you want to add some extra security. Move the
file from <code class="file">/app/config/database_configuration.php</code> to somewhere outside
your <code class="file">htdocs</code> folder and define <code>DATABASE_CONFIGURATION</code>
accordingly. The default value points to the folder in which
<code class="file">config.php</code> is located:

<pre class="brush: php">
define('DATABASE_CONFIGURATION', __DIR__ . DIRECTORY_SEPARATOR . 'database_configuration.php');
</pre>

<p>You can change the name of the file, but do not change the name of the
DatabaseConfiguration class.</p>

<h3>Sessions</h3>

<p>The <code>USESESSION</code> constant simply enables (<code>true</code>) or
disables (<code>false</code>) server-side sessions. Disable it for very simple
websites, but be aware that user authentication requires sessions.</p>

<h3>Authentication</h3>

<p>Setting <code>USEAUTH</code> to true does nothing by itself. Its combination
with the <strong>Users</strong> controller makes user sessions easier. You can
set this to <code>false</code> if you don't require authentication.</p>

<h3>File extensions</h3>

<p>The following <em>hidden</em> constants allow you to set your own file
extensions for the framework files you create:</p>

<ul>
    <li><code>CONTROLLER_EXT</code> &mdash; default is <code class="file">.class.php</code></li>
    <li><code>MODEL_EXT</code> &mdash; default is <code class="file">.class.php</code></li>
    <li><code>VIEW_EXT</code> &mdash; default is <code class="file">.php</code></li>
    <li><code>ELEMENT_EXT</code> &mdash; default is <code class="file">.php</code></li>
    <li><code>LAYOUT_EXT</code> &mdash; default is <code class="file">.layout.php</code></li>
    <li><code>LIBRARY_EXT</code> &mdash; default is <code class="file">.class.php</code></li>
</ul>

<p>Note that extensions require a period (i.e. <code>'.php'</code>) but it does
not need to be the first character. If you want your views to be in the form
<code>my_action_view.html.php</code>, you can define the constant this way:</p>

<pre class="brush: php">
define('VIEW_EXT', '_view.html.php');
</pre>

<h3>Assets folder</h3>

<p>By setting the <code>WWW</code> constant, you can specify the location of the
assets folder (which commonly contains the CSS and JS files). This is useful if
you want to share it among different applications.</p>

<h3>Bootstrapping</h3>

<p>The <code class="file">/app/config/bootstrap.php</code> file is run after the
configuration is loaded. It may contain constants, variable and class
definitions, shared functions and probably something else.</p>

<p>However, he framework will look into the <code class="file">bootstrap.php</code>
file for two functions: <code>custom_hash()</code> and <code>sqlite_init()</code>.</p>

<ul>
    <li>The <code>custom_hash()</code> function is used by the <strong>Auth</strong>
    class to create safe passwords. It receives an array with the login credentials
    and must return a string with the hashed password.</li>
    <li><code>sqlite_init()</code> is called by the <strong>Pew</strong> class
    upon instantiation of the <strong>PewDatabase</strong> class, whenever
    the database engine is set to <code>'sqlite'</code> and the SQLite file is
    empty. Use this function to create tables and insert filler rows.</li>
</ul>

<h3>Predefined constants</h3>

<p>Besides the aforementioned ones, the framework uses a set of constants that
are globally available:</p>

<ul>
    <li><code>DS</code>: Short alias of <code>DIRECTORY_SEPARATOR</code>.
    It's either <code>/</code> (for Linux/Unix/Mac OS X) or <code>\</code>
    (for Windows).</li>
    <li><code>PS</code>: The path separator in the URLs, this is always
    <code>/</code>.</li>
    <li><code>ROOT</code>: Location of the application files in the filesystem.
    For example, <code class="file">c:\apache\htdocs\my_app\</code> or
    <code class="file">/var/www/</code>.</li>
    <li><code>URL</code>: Automatically-detected internet location of the
    web site. May be <code class="file">http://localhost/my_app/</code> in development
    and <code>http://my-app.com/</code> in production.</li>
    <li><code>WWW</code>: Internet location of the assets folder.
    Following the <code>URL</code> example, this would be
    <code class="file">http://my-app.com/www/</code>.</li>
    <li><code>PEW_LOCAL</code>: This constant is <code>true</code> if
    the client is in the same IP address of server (most development
    environments) or remotely (client and server are on different machines).</li>
</ul>

<h3>Custom action controllers and models</h3>

<p>If the framework controllers come short for any reason, there's a way
of extending them and propagating changes to the action controllers defined
in your application.</p>

<p>By creating <code class="file">/app/pew_controller.php</code> and
typing...</p>

<pre class="brush: php">
&lt;?php

class PewController extends Controller
{    
    public function do_something()
    {
        return;
    }
}
</pre>

<p>...and then changing your controller declarations to...</p>

<pre class="brush: php">
&lt;?php

class MyController extends PewController
{
    
}
</pre>

<p>...you will have access to the <code>do_something()</code> method inside your
controllers. Note that <strong>MyController</strong> extends
<strong>PewController</strong> instead of <strong>Controller</strong>.
This mechanism is especially useful to define <code>before_action()</code>
and <code>after_action()</code> methods that apply to many controllers and
are called automatically.</p>
