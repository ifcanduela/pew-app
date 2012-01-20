<h1>The URL Parameters</h1>
            
<p>Having a lot of mostly-static pages won't help you rule the
internet. What if I told you you could parameterize your action
methods? You'd answer <em>"actually, I was expecting that"</em>,
right? Well, I've thought about it.</p>
<p>A URL like <code
class="url">http://example.com/cats/nom-nom-nom/31/catnip/do:meow</code>
will be magically parsed by the framework and create an array field
named <code class="php">$parameters</code> in the controller.</p>
<p><code class="php">pr($this->parameters, 'The parameters')</code>
will output the following block of text wrapped in
<code>&lt;pre></code> tags:</p>

<pre class="php">
The parameters: Array(
    [controller] => <s>'cats'</s>
    [action] => <s>'nom_nom_nom'</s>
    [id] => <i>31</i>
    [0] => <i>31</i>
    [1] => <s>'catnip'</s>
    [do] => <s>'meow'</s>
    [numbered] => Array (
        [0] => <i>31</i>
        [1] => <s>'catnip'</s>
    )
    [named] => Array (
        [id] => <i>31</i>
        [do] => <s>'meow'</s>
    )
)</pre>

<p>So you'll have access to anything the browser sends, and
you'll be able to use it in your controllers. Some rules for the
kids:</p>

<ol>
    <li>The first numeric parameter after the action is
    automatically supposed to be an identifier of sorts.</li>
    <li>If a parameter follows the <code>key:value</code> format, it
    will be considered a named parameter.</li>
    <li>All unnamed parameters after the action will be assigned a
    numeric index.</li>
</ol>
