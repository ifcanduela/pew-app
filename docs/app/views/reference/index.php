<h2>Function reference</h2>

<dl>
    <dt><code>array_flatten($data)</code></dt>
    <dd>
        <p>This function returns an single-dimensional array with all the 
        scalar elements of the original array. Very useful after filtering an
        array with <code>array_reap()</code>.</p>
    </dd>
    
    <dt><code>array_reap($data, $filter)</code></dt>
    <dd>
        <p>Returns a copy of the original array without the elements that don't
        match the specified <code>$filter</code>, which is a string with keywords
        separated by colons (<code>:</code>). Available keywords are 
        <code>$</code> for any string index, <code>#</code> for any number index, 
        an integer or string literals for precise matching. Each successive 
        keyword after a colon is applied to a deeper dimension of the source 
        array.</p>
    </dd>
    
    <dt><code>check_dirs($path)</code3></dt>
    <dd>
        <p>Creates directories to make sure the path received exists. This can
        actually be replicated with the <code>$recursive</code> parameter of the
        <code>mkdir()</code> PHP function in PHP 5.0 and newer, so this function 
        is not that useful anymore.</p>
    </dd>
    
    <dt><code>class_name_to_file_name($class_name)</code></dt>
    <dd>
        <p>Converts a class name in camel-case to file name with words separated
        by underscores.</p>
    </dd>
    
    <dt><code>clean_array_data($post)</code></dt>
    <dd>
        <p></p>
    </dd>
    
    <dt><code>deref(array $array, $index, $strict = false)</code></dt>
    <dd>
        <p></p>
    </dd>
    
    <dt><code>file_name_to_class_name($file_name)</code></dt>
    <dd>
        <p></p>
    </dd>
    
    <dt>
        <code>get_execution_time($partial = false)</code>
    </dt>
    <dt>
        <code>pew_clean_string($evil_string)</code>
    </dt>
    <dt>
        <code>pew_exit($message, $level = E_USER_ERROR)</code>
    </dt>
    <dd></dd>
    <dt>
        <code>pr($data, $title = null)</code>
    </dt>
    <dd>This function prints whatever is passed as argument, prependint the
    optional title, and wrapped between <code>&lt;pre></code> tags except when 
    the script is run from the console. It works regardless of the debug mode.
    </dd>
    <dt>
        <code>print_config()</code>
    </dt>
    <dt>
        <code>redirect($url)</code>
    </dt>
    <dt>
        <code>root($path = '', $print = true)</code>
    </dt>
    <dt>
        <code>sanitize($string)</code>
    </dt>
    <dt>
        <code>slugify($str)</code>
    </dt>
    <dt>
        <code>to_underscores($str)</code>
    </dt>
    <dt>
        <code>url($url = '', $print = true)</code>
    </dt>
    <dt>
        <code>user()</code>
    </dt>
    <dt>
        <code>www($url = '', $print = true)</code>
    </dt>
</dl>