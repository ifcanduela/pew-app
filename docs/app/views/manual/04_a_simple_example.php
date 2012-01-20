<h1>A Simple Example</h1>
            
<p>After downplaying <strong class="pewpewpew">Pew-Pew-Pew</strong> for a few
chapters, it's time to show a disappointing example, don't you think? Create a
new file, write <code class="html">&lt;h1>Kittens!&lt;/h1></code> in it, and
save it as <code class="filesystem">app/views/pages/test.php</code>. Point your
browser to <code class="filesystem">/pages/test</code> and look at your heading,
all bold over white, mirroring the dull smile on your face.</p>

<p>Do the same with some other filename, and insert <code
class="url">&lt;a href="&lt;?php
url('pages/test'); ?>">&lt;/a></code> into the new document. Load the
new page and click the link. I hope that worked.</p>

<h2>Something more involved</h2>

<p>OK, a 10-minute rundown:</p>

<h3>Setup a database</h3>

<ol>
    <li>Create a MySQL database called <code>whatever</code>, with a table
        called <code>hoominz</code> and a table called <code>kittehs</code>.</li>
    <li>Both tables should have an <code>id</code> field (integer,
        auto-increment, not null, primary key) and a <code>name</code> field
        (varchar).</li>
    <li>The <code>hoominz</code> table must have a <code>kitteh_id</code> field
        (integer).</li>
    <li>Don't forget to add some dummy data; a couple of kittehs (note their id
        values) and some hoominz for each kitteh.</li>
    <li>Edit <code
    class="filesystem">app/config/database_configuration.php</code> to suit
    your configuration.</li>
</ol>

<h3>Create Models</h3>

<ol>
    <li>Create <code>hoominz_model.class.php</code> and
        <code>kittehs_model.class.php</code> in <code>app/models/</code>, with
        the following code:
    <pre class="brush: php">
# hoominz_model.class.php
class HoominzModel extends Model
{
    public $belongs_to = array('kittehs' => 'kitteh_id');
}

# kittehs_model.class.php -- actually, this is not needed
class KittehsModel extends Model
{
    public $has_many = array('hoominz' => 'kitteh_id');
}    
</pre></li>
</ol>



<h3>Hoominz Controller</h3>

<ol>
    <li>Create hoominz.class.php in app/controllers
    <pre class="brush: php">
class Hoominz extends Controller
{
    public function index()
    {
        if ($this->parameters['id']) {
            $hoomin = $this->db->find($this->parameters['id']);
            $kitteh =
                $this->db->kittehs->find($hoomin['kitteh_id']);
            
            $this->data['hoominz'] = compact('hoomin', 'kitteh');
        } else {
            $this->data['hoominz'] = $this->db->find_all();
        }
    }
}</pre></li>
    <li><em>Logic!</em> That will do for now.</li>
</ol>

<h3>The View</h3>

<ol>
    <li>Put the following text in <code
        class="filesystem">app/views/hoominz/index.php</code>.
    <pre class="brush: php">&lt;?php pr($hoominz, 'The Humans'); ?></pre>
    </li>
    <li>If you visit <a
    href="http://localhost/books/index/1">http://localhost/books/index/1</a>
    you will get a beautiful debug message with the humans number 1 and its cat.
    If you remove the final /1 in the URL, you'll see all humans, thanks to
    that <code>if</code> we put up there.</li>
    <li>Should you desire to also fetch the cats' names for the humans in the
    <code>else</code> branch, you should loop through the <code
    class="method">find_all()</code> results in the controller and call the
    <code class="method">find($id)</code> method of <code
    class="class">KittehsModel</code> with the <code
    class="variable">$kitteh_id</code> of each human. I know this could be
    automated, but I prefer this kind of manual control.
    </li>
    <li>However, if we were searching for cats, their children (the humans)
    would have been fetched automatically when calling <code
    class="method">find()</code>:
    <pre class="brush: php"># $kitteh['hoominz'] will contain the related hoominz automagically
$kitteh = $this->db->find($the_kitteh_id);</pre>
    </li>
</ol>

<h3>Keep At It</h3>

<p>Continue by creating a layout. May I suggest you copy <code
class="filesystem">sys/default/views/pewpewpew.layout.php</code> to <code
class="filesystem">app/views/</code>? Edit that file and <code
class="filesystem">www/css/default.css</code> to flavour the look of the pages
to your tastes. That'll give you an idea of what to put in the
layout and the views.</p>

<p>Next, improve upon the view. A debug dump won't earn you any design
accolades.</p>
