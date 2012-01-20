<h2>Elements</h2>

<p>Elements are small snippets of your application views that you can reuse.
The usefulness of elements is directly proportional to how many different
view configrations you have. A site with a very homogeneous appearance
should build most of the final HTML markup with a single layout and many
elements. To continue the example from the <a href="<?php url('book/layouts'); ?>" 
title="Layouts">previous chapter</a>, the layout file for this Documentation
uses elements for the sidebar navigation and the chapter navigation, which 
are common to every single page in the application.</p>

<pre class="brush: php">
&lt;div id="sidebar">
	&lt;ul>
	&lt;?php foreach ($this->index as $page): list($k, $v) = $page ?>
		&lt;li>
			&lt;a &lt;?php if ($k === $this->parameters['action']): ?> class="active" &lt;?php endif; ?>
				href="&lt;?php url("$this->file_name/$k"); ?>">&lt;?php echo $v; ?>&lt;/a>
		&lt;/li>
	&lt;?php endforeach; ?>
	&lt;/ul>
&lt;/div>
</pre>

<p>There is something going on behind the scenes: the <strong>Book</strong>, 
<strong>Tutorial</strong> and <strong>Reference</strong> controllers have a 
custom <code>$index</code> property with an index of the pages. Otherwise, 
this is an exemplary element: it produces a snippet that is valid for every 
single page in the application.</p>

<p>You can call elements from the view files and from the layout files.</p>
