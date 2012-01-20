<h2>Views</h2>

<p>Views are probably the easiest part to understand about the framework, but
they still have their quirks.</p>

<p>View files are mostly HTML documents with access to some variables created
previously by the controller action. Apart from those variables, you can 
access everything else in the current controller's scope through the <code>$this</code> 
variable, since views are executed inside the <strong>Controller</strong> scope.</p>

<p>View variables are created in the action by adding indexes to the <code>$data</code>
array:</p>

<pre class="brush: php">
&lt;?php

class Cats extends Controller
{
	public function nap($length)
	{
		$this->data['nap_length'] = $length;
	} 
}
</pre>

<p>The <code>$nap_length</code> variable will be available to the 
<code class="file">/views/cats/nap.php</code> view:</p>

<pre class="brush: php">
<p>It's time for a <span>&lt;?php echo $nap_length; ?></span>-minute nap!</p>
</pre>

<h3>Links and resources</h3>

<p>Whlie creating a view, it's often desired to type a </p>

<h3>Using Twig</h3>

<aside>This feature is currently undergoing testing.</aside>

<p><a href="http://twig.sensiolabs.org/">Twig</a> is a template engine from the guy behind
Symfony, which means it's good. <span class="pewpewpew">Pew-Pew-Pew</span> has support
for Twig template syntax in view files. Of course, it requires some configuration.</p>

<aside>Support for Twig syntax inside layouts and elements is not yet implemented.</aside>

<p>The first step is the installation of Twig. The recommended method is via PEAR:</p>

<pre>
pear channel-discover pear.twig-project.org
pear install twig/Twig
</pre>

<p>That takes care of the library quickly. The framework will search in your
currently-configured include directories.</p>

<p>If you already have Twig somewhere 
else, you'll need to provide some directions to the framework, specifically
the directions to Twig's <strong>Autoloader</strong> class. The easiest way
is through the <code>\app\config\bootstrap.php</code> file:</p>

<pre>
&lt;?php

require 'path/to/Twig/Autoloader.php'
</pre>

<p>Hopefully <span class="pewpewpew">Pew-pew-pew</span> will take care of 
everything else, but sometimes there are unexpected errors.</p>

<p>Once Twig is installed, you need to activate it by adding this line to 
<code class="file">/app/config/config.php</code>:</p>

<pre class="brush: php">
define('USETWIG', true);
</pre>

<p>If you run your app and see no errors... there are no errors! An easy way to
test it is to open one of your views and putting <code>{{ this.title }}</code>
somewhere, and see if the page title is correctly printed. The previous view
for the <code>nap()</code> action would be like this:</p>

<pre class="brush: php">
<p>It's time for a <span>{{ nap_length }}</span>-minute nap!</p>
</pre>

<p>The <code>USETWIG</code> setting can also be set inside the controller:</p>

<pre class="brush: php">
$this->use_twig = true;
</pre>

<p>Go to the <a href="http://twig.sensiolabs.org/doc/templates.html" 
title="Twig documentation">Twig for template designers</a> documentation for a 
complete reference of the Twig templating syntax.</p>
