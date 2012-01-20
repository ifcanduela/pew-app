<h2>Check out the layout</h2>

<p>It's been a long time coming, but here is it at last: this is the usual
request flow in Pew-Pew-Pew.</p>

<ol>
    <li>The server receives a URL request</li>
    <li>The .htaccess file transforms it into something Pew-Pew-Pew can understand</li>
    <li>The App controller instantiates the requested controller</li>
    <li>The App controller invokes the requested action, which is a method of the instantiated controller</li>
    <li>The action feeds some data into the controller itself</li>
    <li>The App controller calls the view method of the controller and stores the result (usually some HTML stuff)</li>
    <li>The app controller inserts the view output inside the layout file</li>
    <li>The server sends the layout file, complete with the view output, to the requester</li>
</ol>

<p>This somewhat explains the role of the layout file in the process. In practice,
it's like a frame for a picture. The default layout sets the doctype, the
character set encoding and the document title, includes some CSS and JavaScript
files and that's it. Whatever is displayed in the browser is the exclusive
responsibility of the view.</p>

<p>You can (<em>should</em>) create more elaborate layouts, but always
remember to at least use this piece of code, since it includes the view
output.</p>

<pre class="brush: php">
&lt;?php echo $this->output; ?>
</pre>

<p>There's also <code>$this->title;</code>, which stores the auto-generated
page title.</p>

<h3>Elements</h3>

<p>Elements have limited usefulness, but they're helpful nonetheless. They
are small snippets that you can use in any views. For example, if you have
a <em>Submit a Comment</em> form in more than one place, you can put
the form in an element, save it to
<code class="file">/app/views/elements/comment_form.php</code> and use this
to load it whenever you need it:</p>

<pre class="brush: php">
&lt;?php

$this->element('comment_form', array('post_id' => $post['id']));
</pre>

<p>The second parameter is an associative array that will make the
<code>$post_id</code> variable available inside the element file.</p>
