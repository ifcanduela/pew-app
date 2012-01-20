<h2>Models</h2>

<p>Simply put, models are objects used to access the database. The models in
<span class="pewpewpew">Pew-Pew-Pew</span> are not as powerful as those found
in other frameworks, but they provide a simple way to interact with the database
while automating a bit of work.</p>

<p>Models are accessed through the <code>$model</code> property of the 
controller. The model property is only initialized upon first access, a 
technique called <em>lazy loading</em>. This means that the model for controller
<strong>Cats</strong> does not exist until you try to read 
<code>$this->model</code>.</p>

<p>There's only one condition to use the controller's model: there must be a table
with the proper name in the database. If that condition is met, you can read 
from and write to it using the <strong>Model</strong> class methods.</p>

<h3>Fetching data</h3>

<p>These are methods for selecting and counting rows.</p>

<dl>
    <dt><code>find($id)</code> and <code>find_by_*($value)</code></dt>
    
    <dd>
        <p>The <code>find()</code> method retrieves a single row matching the 
        primary key value provided. Rows are returned as associative arrays, 
        with a string index for every table column. If there is no match, 
        <code>false</code> is returned.</p>

<pre class="brush: php">
$this->model->find(42);
</pre>
        
        <p>Additionally, you can search by field by passing an associative array
        (that also accepts any other condition):</p>

<pre class="brush: php">
$this->model->find(array('name' => 'Mittens'));
</pre>
        
        <p>However, <code>find_by_*()</code> is more convenient for searching
        rows filtering by a column other than the primary key. It fetches a row 
        matching the value provided for the column specified in the function 
        name. If there's a <code>name</code> column in your <code>cats</code> 
        table, you can do this:</p>
        
<pre class="brush: php">
$this->model->find_by_name('Mittens');
</pre>
    </dd>
    
    <dt><code>find_all()</code> and <code>find_all_by_*($id)</code></dt>
    
    <dd>
        <p>These two methods are tht multi-row equivalents to 
        <code>find()</code> and <code>find_by_*()</code>. They return all row in 
        the table, in an indexed array, or <code>false</code> if there are no 
        records:</p>

<pre class="brush: php">
$this->model->find_all();
$this->model->find_all(array('fur_color' => 'orange'));
$this->model->find_all_by_fur_color('orange'));
</pre>
    </dd>
</dl>

<h3>Query modifiers</h3>

<p>Selecting data with <code>find()</code> and <code>find_all()</code> is easy
and streamlined, but sometimes your queries need more fine-tuning. For that 
reason there are additional methods that allow you to specify SQL 
clauses:</p>

<dl>
    <dt><code>where($conditions)</code></dt>
    
    <dd>
        <p>This method accepts an associative array of conditions. The returned
        records must meet all conditions.</p>

<pre class="brush: php">
$this->model->where(array('name' => array('like', 'Whi%'), 'fur_color' => 'orange')->find_all();
</pre>
    </dd>

    <dt><code>order_by($fields)</code></dt>
    
    <dd>
        <p>Accepts a SQL-formatted string of fields and sorting qualifiers:</p>
        
<pre class="brush: php">
$this->model->order_by('birthday DESC, name')->find_all();
</pre>
    </dd>
    
    <dt><code>group_by($fields)</code></dt>
    
    <dd>
        <p>Accepts a SQL-formatted string of fields:</p>
        
<pre class="brush: php">
$this->model->group_by('fur_color')->find_all();
</pre>
    </dd>
    
    <aside>Please note that this query modifier does not yet work properly.</aside>
    
    <dt><code>having($conditions)</code></dt>
    
    <dd>
        <p>Specifies conditions that a SQL-formatted string of fields:</p>
        
<pre class="brush: php">
$this->model->group_by('fur_color')->having(array('eye_color' => 'yellow'))->find_all();
</pre>
    </dd>
    
    <aside>Please note that this query modifier does not yet work properly.</aside>
    
    <dt><code>limit($how_many, [$offset])</code></dt>
    
    <dd>
        <p>Sets a maximum number of rows to return, and an optional starting row.</p>
        
<pre class="brush: php">
# retrieve the first 20 rows (0 to 19)
$this->model->limit(20)->find_all();
# retrieve rows 20 to 39
$this->model->limit(20, 20)->find_all();
</pre>
    </dd>
</dl>

<h3>Inserting, updating and deleting data</h3>

<p>Saving data to the database is very easy. The <code>save()</code> method
receives an associative array with column names as indexes. If the primary
key field of the table is not present, the <code>save()</code> method performs
an <code>INSERT</code>, adding a new record to the table. Otherwise, the method 
executes an <code>UPDATE</code> statement, saving the fields present in the 
array.</p>

<pre class="brush: php">
&lt;?php

class Cats extends Controller
{
    public function omnomnom()
    {
        $whiskers = array(
            'name' => 'Whiskers',
            'fur_color' => 'orange',
            'eye_color' => 'yellow'
        );

        $mittens = array(
            'id' => 1
            'name' => 'Mittens',
            'fur_color' => 'white',
            'eye_color' => 'blue'
        );
        # inserts Whiskers into the 'cats' table
        $this->model->save($whiskers); 

        # updates Mittens' data in the 'cats' table
        $this->model->save($mittens);   
    }
}
</pre>

<p>The <code>delete()</code> method is equally simple to use, but offers a little
more flexibility. If you pass it a single value, it will delete all records that
match that value to their primary key (usually just one, of course). You can
pass it an associative array with column/value pairs, to delete rows selectively.
If you don't pass a parameter, the <code>delete()</code> method will use the
conditions you passed with <code>where()</code> method. Finally, if you
call it with <code>delete(true)</code>, the method will delete every record in 
the table, so be careful.</p>

<pre class="brush: php">
&lt;?php

class Cats extends Controller
{
    public function harbls()
    {
        $basement_cats = array(
            'fur_color' => 'black',
            'eye_color' => 'yellow'
        );

        $ceiling_cats = array(
            'fur_color' => 'white',
            'eye_color' => 'blue'
        );

        # deletes Mittens
        $this->model->delete(1);

        # deletes all black/yellow cats
        $this->model->delete($basement_cats);

        # deletes all white/blue cats
        $this->model->where($ceiling_cats)->delete();
    }
}
</pre>

<h3>Related models</h3>

<p>By defining relationships between models you can automate the retrieval of
related datasets. For example, if you had a <code>races</code> table in the 
database and a <code>race_id</code> column in the <code>cats</code> table,
you'd like to automatically fetch information about the race when you search
for a cat:</p>

<pre class="brush: php">
# find everything about Whiskers
$whiskers = $this->model->find_by_name('Whiskers');

# echo out the race name
echo $whiskers['races']['name'];
</pre>

<p>Relationships can be specified <em>on-the-fly</em>, in a controller action, 
or by creating a <em>concrete</em> model for the controller. To specify 
relationships from the controller (and fetch related data) you use the
<code>add_parent()</code> and <code>add_child()</code> model methods:</p>

<pre class="brush: php">
# link the 'races' table
$this->model->add_parent('races', 'race_id');

# state that you want to get related data alongside the cat data
$this->model->find_related(true);

# and run the search
$this->model->find_all();
</pre>

<p>Every result will have this format:</p>

<pre>
array (
    [0] => array (
        'id' => 1,
        'name' => 'Mittens',
        'fur_color' => 'white',
        'eye_color' => 'blue',
        'race_id' => 3,
        'races' => array (
            'id' => 3,
            'name' => 'Heavenly fluff'
        )
    ),
    [1] => array ( ... )
)
</pre>

<p>Since a cat belongs to a race, we use <code>add_parent()</code> to tell just
that to the model. If we were fetching races and wanted to get all cats 
belonging to them, we'd do this:</p>

<pre class="brush: php">
# link the 'cats' table
$this->model->add_child('cats', 'race_id');

# state that you want to get related data alongside the race data
$this->model->find_related(true);

# and run the search
$this->model->find(3);
</pre>

<p>And this would be the result:</p>

<pre>
array (
    'id' => 3,
    'name' => 'Heavenly fluff',
    'cats' => array (
        [0] => array (
            'id' => 1,
            'name' => 'Mittens',
            'fur_color' => 'white',
            'eye_color' => 'blue',
            'race_id' => 3,
    
        ),
        [1] => array (
            'id' => 3,
            'name' => 'Professor Smilie',
            'fur_color' => 'gray',
            'eye_color' => 'green',
            'race_id' => 3,
        )
    )
)
</pre>

<p>By creating custom <strong>Cats</strong> and <strong>Races</strong> models we 
can make that the default behavior. This goes in 
<code class="file">cats_model.class.php</code>, for example:</p>

<pre class="brush: php">
&lt;?php

class CatsModel extends Model
{
    public $belongs_to = array('races', 'race_id');
}
</pre>

<p>The <code>find()</code> functions default to <em>not</em> fetching related
data, so the <code>find_related()</code> function must be called before any search
for which we want to query related models.</p>
