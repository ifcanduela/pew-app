<h2>Actions</h2>

<p>Actions contain the bulk of most <span class="pewpewpew">Pew-Pew-Pew</span> 
applications, as they contain the general system logic like database queries and
view setup.</p>

<p>Actions are implemented as methods of a class that extends Controller. Their 
names are automatically matched to the second segment of the URL (or 
<code>index</code> on its absence), and after an action completes the view is
automatically rendered. Of course, view rendering can be prevented, and both the
layout file and the view file that get rendered can be changed.</p>

<h3>Arguments</h3>

<p>Actions, being functions, can receive call arguments. In this case, the 
arguments passed to an action function are all the segments except the first two,
that correspond to controller and action, and any named segment (with a colon 
separating name and value). An example action:</p>

<pre class="brush: php">
public function eat($type, $amount)
</pre>

<p>And a few valid URLs.:
<h3>Non-action methods</h3>

<p>You can make a controller method invisible to the URL matching by prefixing
its name with an underscore. An action named <code>pounce()</code> will be 
accessible via <code class="file">http://i-love-ca.ts/cats/pounce</code>, but if its 
name is <code>_pounce()</code>, it will be forbidden.</p>

<h3>Alternate layouts</h3>

<p>In the same manner that actions prefixed with an underscore are forbidden,
actions prefixed by either an at (<code>@</code>) simbol or a colon 
(<code>:</code>) get special treatment. For example, if the URL is like this:</p>

<pre>http://i-love-ca.ts/cats/:sleep</pre>

<p>The framework will use the <code>sleep()</code> action, but will actually
use a <code class="file">json.layout.php</code> file, if it exists. If it does 
not, no view will be rendered (in this case, the output can be echoed directly
inside the action). An at symbol (<code>cats/@sleep</code>) would use the
<code class="file">json.layout.php</code> file.</p>

<p>A simple example of the <code class="file">json.layout.php</code> layout 
could be this:</p>

<pre class="brush: php">
&lt;?php
echo json_encode($this->data);
</pre>

