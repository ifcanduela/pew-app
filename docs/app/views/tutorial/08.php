<div class="tutorial_navigation">
    <a href="<?php url('tutorial/07'); ?>" class="prev">&laquo; Cheap manpower</a>
    <a href="<?php url('tutorial/09'); ?>" class="next">Modeling the app &raquo;</a>
</div>

<h2>About you</h2>

<p>Creating an about page is also easy. First create
<code class="file">blog/app/views/pages/about.php:</code></p>

<pre class="brush: xml">
&lt;h2>About me&lt;/h2>
&lt;p>I'd like to seize the opportunity to tell you you're very brave and
a model for future generations, just like I am.&lt;/p>
</pre>

<p>Then, go to <code>http://localhost/blog/pages/about</code>. Done. Next chapter.</p>

<p>If you <em>absolutely</em> require explanations, well... The Pages controller
takes the <code>action</code> parameter and tries to render a view with that
name. Nothing else. It has no controller logic, and that mean most views handled
by the Pages controller are static HTML. Of course, you can add some PHP stuff
in the view, or make an Ajax app using a pages view as landing page.</p>

<div class="tutorial_navigation">
    <a href="<?php url('tutorial/07'); ?>" class="prev">&laquo; Cheap manpower</a>
    <a href="<?php url('tutorial/09'); ?>" class="next">Modeling the app &raquo;</a>
</div>