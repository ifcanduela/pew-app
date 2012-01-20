<h2>Users and authentication</h2>

<p>Users are important for blogs. Noy only the editors, who provide content,
but the visitors, who actually make the content important, contribute
comments and try to hack into your admin section.</p>

<p>In this short chapter we'll add an administrator user to the database
directly, and I'll try to explain how Pew-Pew-Pew automates some of the
login and logout chores.</p>

<p>If we take a look at the <code>users</code> table, we'll see there's a
password field there. By default, Pew-Pew-Pew just calculates an MD5 hash
of the user's password and stores it in the database. If you want a more
refined password hashing algorithm (you should), you can have it by
creating a <code>custom_hash</code> function in
<code class="file">/blog/app/config/bootstrap.php</code>, like this one
that I'll be using:</p>
    
<pre class="brush: php">
&lt;?php

function custom_hash($data)
{
    return md5($data['username'] . md5($data['password']));
}
</pre>
    
<p>The <code>$data</code> argument contains the information that the user
is required to enter at login. So maybe you end up with <code>email</code>
and <code>passwd</code> fields instead of the ones here. This function would
create a slightly more complex hash that would, at least, hinder dictionary
attacks.</p>

<p>The resulting hash for the admin user, using password "<code>admin</code>",
would be:</p>
    
<pre>md5("admin" + md5("admin")) = "c0e024d9200b5705bc4804722636378a"</pre>
    
<p>Use this knowledge to insert the user in the database. Open your SQLite
manager of choice and run the following command:</p>

<pre class="brush: sql">
INSERT INTO users (username, password, email, role)
VALUES ('admin', 'c0e024d9200b5705bc4804722636378a', 'admin@localhost', 0);
</pre>
    
<p>With that user there, I would make a backup copy of the database file.</p>
    
<p>The last step is to copy the default Users controller from
<code class="file">/sys/default/controllers/users.class.php</code> to
your <code class="file">/app/controllers</code> folder. This is done so
we can modify it later. The files in the <code class="file">/default</code>
folder location are only used when there's no file like it in the
<code class="file">/app</code> folder.</p>
    
<p>Now we have a functional user authentication mechanism, and we'll be
using it in a few chapters. The login page will be at
<a href="http://localhost/blog/users/login">http://localhost/blog/users/login</a>
and there's also a logout page at
<a href="http://localhost/blog/users/logout">http://localhost/blog/users/logout</a>.</p>
