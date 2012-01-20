<h1>Models And Database Access</h1>

<p>Database configuration is very straightforward. Only MySQL databases are
supported, and the configuration parameters are entered into the <code
class="filesystem">app/config/database_configuration.php</code> file. Further
configuration (table names, common fields, or whether to use a database or not)
can be specified in a controller basis.</p>

<p>A base model is available from the get-go, and is automatically initialised
by the controller if USEDB is true. It provides the expected <code
class="method">find()</code>, <code class="method">find_all()</code> and <code
class="method">save()</code> methods. Take into account that a strongly
convention-compliant database setup is required for this to work. And it may not
work at all, I'm not sure.</p>

<p>If you want to write your own models drop them in the <code
class="filesystem">app/models</code> and name them, for example, <code
class="filesystem">bukkit_model.class.php</code> (supposing your controller is
called <code class="filesystem">bukkit.class.php</code>, of course). It may be
a little long-winded, but they're optional. Model classes may be empty&mdash;And
they would be functional, too:</p>

<pre class="brush: php">
class BukkitModel extends Model
{
    /**
      * This empty model will be enough to access the bukkit
      * table in the db, but you can override the table name or
      * the primary key fields, add relationships or create
      * custom methods.
      *
      * "Nice to know, Bob!"
      */
}
</pre>
