<h2>Libraries</h2>

<p>Libraries are just additional classes with additional methods that you can share
between controllers. They don't have any special requirement beyond their filenames,
which must conform to the same rules as the controller and model classes: 
class names start with an upper-case letter and don't contain underscores, 
and every following upper-case letter is transformed to a lower-case letter 
preceded by an underscore in the file name.</p>

<pre>
class name        file name
------------------------------------------
Formatter         app/libs/formatter.php
FileReader        app/libs/file_reader.php
Filereader        app/libs/filereader.php
CSVParser         app/libs/c_s_v_parser.php
File_Reader       - not supported
fileReader        - not supported
</pre>

<p>To use libraries, you do this:</p>

<pre class="brush: php">
&lt;?php

class Cats extends Controller
{
    $libs = array('FileReader', 'Formatter', CSVParser');

    public function eat($food)
    {
        $file = $this->FileReader->read($food . $this->FileReader::EXTENSION);
        $this->data['file'] = $this->CSVParser->parse($file);
    }
}
</pre>

<p>That code snippet shows that libraries are accessed as controller properties,
with their class names as propertiy names. These properties are also available
in the views:
</p>

<pre class="brush: php">
<textarea>&lt;?php echo $this->Formatter->format($file); ?></textarea>
</pre>
