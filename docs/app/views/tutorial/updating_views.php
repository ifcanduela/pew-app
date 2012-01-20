<h2>Updating the app</h2>

<p>This chapter is going to be a little more <em>do it yourself</em> than any
previous one, since we're making many changes to a few files. This is what
we're changing:</p>

<ul>
    <li>We're adding "edit" links to the posts index, protected by user role
        checks</li>
    <li>We're changing <code>add()</code> and <code>edit()</code>
        actions to require authentication</li>
    <li></li>
    <li></li>
</ul>

<pre>
    user role checks in posts/index view
    require_auth in posts and comments
    user role checks in posts
</pre>