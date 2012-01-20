<h1>Views, Elements And Layouts</h1>

<p>In order to make displaying a bunch of <code
class="php">$name</code> and <code
class="php">$logo_img['src']['filename_ext']</code> strings less of
a chore, <strong class="pewpewpew">Pew-Pew-Pew</strong> does
absolutely nothing. PHP/HTML interaction is still painful, and you
won't find anything like Facebook's <a
href="http://www.facebook.com/notes/facebook-engineering/xhp-a-new-way-to-write-php/294003943919">XHP</a>
here. You won't even get <a href="http://www.smarty.net/">Smarty</a>
templates. I hate <code class="php">echo</code> too, but will never
do anything about it. I prefer <em>passive-aggressive
misbehavior</em>.</p>

<p>Views are called automatically after the action completes, but
this can be overriden. If you want to have data from the action
available in the view, use the <code class="php">$this->data</code>
associative array. An example of this mess:</p>

<pre class="brush: php">
function my_action()
{
    # retrieve some data
    $item_with_id_equal_to_1 = $this->db->find(1);
    # make it visible to the view
    $this->data['item'] = $item_with_id_equal_to_1;

    # change the default my_view view for my_other_view
    $this->view = 'my_other_view';

    # change the default layout
    $this->layout = 'ajax';

    # or prevent rendering altogether
    $this->render = false;
}</pre>

<p>The <code class="filesystem">my_action.php</code> view will have
access to a variable called <code class="php">$item</code>, that
will hold the database result. Additionally, you can call <code
class="php">$this->element()</code> in the view. It will load a
sub-view from the <code class="filesystem">views/elements</code>
folder:</p>

<pre class="brush: php">
$element_params = array('element_var' => $item);
$this->element('navigation', $element_params);</pre>

<p>As you can see with the proper use of your eyes, elements accept
their own arguments, received via an array. <code
class="php">$element_var</code> will be magically available in the
<code class="filesystem">app/views/elements/navigation.php</code>
script.</p>

<p>But you can't be bothered to include the same header and footer
elements in every view you create, can you?. Don't worry, all views
are, by default, wrapped inside something called a <em>layout</em>.
The default layout is only an HTML template that echoes the <code
class="php">$title</code> and the <code
class="php">$content_for_layout</code> (which is the output of the
view itself). You should modify the default layout, or create your
own, and add all the script and style and link and meta tags that
kittens hate.</p>