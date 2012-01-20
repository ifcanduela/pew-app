<h2>Framework overview</h2>

<p>Using the framework is really easy, but building an application is still 
difficult if you don't understand the principles behind it. Its 
<em>paradigm</em>, if you will. Its philosophy, its ethos. Let's 
introspect.</p>

<h3>Views</h3>

<p>The main thing you will be creating will be <em>views</em>. They represent
the result of a browser request. If a visitor requests an ordered list of your pets,
you will have a view that lists your pets, in order. Views are mostly HTML,
although I usually put some JavaScript there too.</p>

<p>Since most views in your website will need to share the same look-and-feel,
you will put most of the global structural HTML in some other file, called a
<em>layout</em>. You can have as many layouts as you want, event a different
one for every possible view. That's not necessarily useful, though. Usually,
layouts contain the <code>HTML</code> and <code>HEAD</code> HTML tags,
including your ever-present <code>LINK</code>, <code>SCRIPT</code> and
<code>TITLE</code> tags. Views are inserted into layouts.</p>

<h3>Controllers</h3>

<p>The second most-common item of the framework are <em>controllers</em>.
These PHP classes represent entities in your application. An online car catalog
would, for example, have controllers for catalogs, manufacturers and car models.
In practice, though, they are a way of grouping <em>actions</em>.</p>

<p>Actions are things the application performs with controllers. You want to
add a manufacturer? You should probably put the source code for that in an action
called <code>add</code> inside the <code>Manufacturers</code> controller, then
try to access it via <code>http://nifty-car-catalog.com/manufacturer/add</code>.</p>

<p>As you can see, controllers and actions map to URLs. This has the benefit of
letting everybody know what they are doing easily.</p>

<p>Controllers are divided in two types: action controllers and pages controllers.
The simple Pages controller is simply a way to load mostly-static web pages. There
are no actions involved, although you can still do some programming in the view.

<p>Action controllers will be the bulk of most applications, and allow you to define
your own actions. Inside actions you will be fetching data from files or databases,
transforming them and sending that processed data to the view for formatting and
display.</p>

<p>But what data? The one you get through the model, maybe.</p>

<h3>Models</h3>

<p>Most web sites will be using some kind of database. Pew-Pew-Pew supports both
MySQL and SQLite databases. To query and update them, the actions inside your
controllers can use special objects called <em>models</em>, that provide some
easy-to-use methods to find, insert, update and delete data from tables.</p>

<p>In the overall structure of the application, you will want to match your
controllers to database tables. Following from the car catalog example, your
database probably has a <code>manufacturers</code> table and a
<code>car_model</code> table. Those are usually the controllers you create. And
every controller will have its own model, bridging the gap between it and its
corresponding database table. Interactions between the controller and the
database table are defined in the model class.</p>

<p>Note, however, that Pew-Pew-Pew provides a default model, with some standard
data-access methods built-in, for all controllers, so defining a model is sometimes
not necessary.</p>

<h3>Everything!</h3>

<p>Putting everything together, we get a process like this:</p>

<ol>
    <li>The user's browser makes a request to our server</li>
    <li>The server loads our application's index.php file</li>
    <li>The index.php file starts our application up</li>
    <li>The application loads the appropriate controller</li>
    <li>The application calls the controller's action</li>
    <li>The action uses the model to, for example, fetch a list of miscellaneous information from the database</li>
    <li>The application takes the data resulting of the action and passes it to the corresponding view</li>
    <li>The output of the view is inserted into the layout</li>
    <li>The final web page is sent to the user's browser</li>
</ol>

<p>Looks complicated, but take in mind that most of it is actually <em>automated</em>.</p>
