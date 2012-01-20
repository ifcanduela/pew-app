<h1>Definitions And Conventions</h1>
            
<p>The units of work are <code class="class">Controllers</code>, <code
class="method">Actions</code> and <code class="method">Views</code>. <code
class="class">Controllers</code> are PHP classes that extend the framework's
<code class="class">Controller</code> class, and they implement methods called
<code class="method">Actions</code>. Most Actions perform some kind of data
processing based on <abbr title="Uniform Resource Locator">URL</abbr>
parameters, POST data and session variables, and setup some information for the
<code class="method">View</code> (including database query results or the title
of the window, for example), which finally gets rendered inside a
<em>layout</em>.</p>

<p>To make things fast, files must be placed in the correct folders, named
according to some rules, and adhere to some special format. Don't fret, this is
pretty easy to remember.</p>

<p><code class="class">Controllers</code>, being <strong>classes</strong>, have
their names in CamelCase notation, written inside files with underscored_names
with <code class="filesystem">.class.php</code> suffix, and living in the <code
class="filesystem">app/controllers</code> folder. Some examples of file-to-class
name mapping:</p>

<ul>
    <li>Class <code class="class">Kitten</code> must be declared in <code
        class="filesystem">app/controllers/kitten.class.php</code></li>
    <li>Class <code class="class">NomNomNom</code> must be declared in <code
        class="filesystem">nom_nom_nom.class.php</code>.</li>
    <li>Class <code class="class">Doggies</code> must go in <code
        class="filesystem">doggies.class.php</code></li>
    <li>Class <code class="class">LOLcat</code> must go in <code
        class="filesystem">l_o_lcat.class.php</code></li>
    <li>Class <code class="class">hairLicker</code> is incorrectly named and
        won't work as expected.</li>
</ul>

<p>Inside the Controllers you must define the Actions, which are just methods
that correspond to things you can do with the subject of the controller. For
example, if the web app allows the visitor to add users, you would create an
<code class="method">add</code> method inside the <code
class="class">User</code> class. URLs for actions can use a dash or an
underscore as name of the action: both
<code class="url">http://example.com/user/add-to-group</code> and
<code class="url">http://example.com/user/add_to_group</code> will point to
$user->add_to_group().</p>

<p>The views are mostly HTML documents, with some PHP thrown in to spice things
up, and can have any name you want, but if they are called after the action they
provide a face for, they will be used automatically. All views are rendered
inside a template called <strong>Layout</strong>. All actions can override the
default layout and view files used for output. Views can include snippets called
<strong>Elements</strong>, which are able to use data passed from the view.</p>

<p>If this sounds convoluted, wait until you reach some of the other stuff. By
chapter 8, you'll wish you'd chosen <a
href="http://www.djangoproject.com/">Django</a>. (Hahaha, no way in
<em><strong>hell</strong></em>).</p>
