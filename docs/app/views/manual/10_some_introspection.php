<h1>Some Introspection</h1>

<p>The code is lazily documented, so you can just take a look at it and see what
it does. It's also prepared for parsing with PhpDocumentor (why don't yhey fix
the .cs files?), and most of the DocBlock stuff works with <a
href="http://www.activestate.com/komodo-edit" title="InstaWin!">Komodo Edit</a>
and NetBeans.</p>

<p>The following classes are available either to extend them or to use them
inside controllers, actions and views. The listed methods are the ones you
should be using.</p>

<ul>
    <li>App: leave this alone</li>
    <li>Controller: extend this class</li>
    <li>Model: extend this class
        <ul>
            <li>find()</li>
            <li>find_all()</li>
            <li>save()</li>
            <li>delete()</li>
        </ul>
    </li>
    <li>Pages: extend this class to create other static controllers
        <ul>
            <li>action()</li>
        </ul>
    </li>
    <li>Session: leave this alone
        <ul>
            <li>write()</li>
            <li>read()</li>
            <li>exists()</li>
            <li>delete()</li>
            <li>set_flash()</li>
            <li>get_flash()</li>
        </ul>
    </li>
    <li>Database: leave this alone
        <ul>
            <li>select()</li>
            <li>single()</li>
            <li>insert()</li>
            <li>update()</li>
            <li>delete()</li>
        </ul>
    </li>
    <li>Auth: leave this alone
        <ul>
            <li>configure()</li>
            <li>authenticate()</li>
            <li>user()</li>
        </ul>
    </li>
    <li>Log: change <code class="filesystem">app/views/elements/debug.php</code>
        instead of this class
        <ul>
            <li>in()</li>
            <li>out()</li>
        </ul>
    </li>
</ul>

<p>And there are some utility functions also visible from anywhere. You're going
to need to refer to the DocBlocks in <code
class="filesystem">sys/functions.php</code>, as I'm too lazy to explain
everything again.</p>

<ul>
    <li>pr(mixed $var, string $title)</li>
    <li>slugify(string $str)</li>
    <li>redirect(string $path)</li>
    <li>root([string $file])</li>
    <li>url([string $path])</li>
    <li>file_name_to_class_name(string $file_name)</li>
    <li>class_name_to_file_name(string $class_name)</li>
</ul>

<p>Okay, that was awful, wasn't it? I'll try to have some kind of <abbr
title="Application Programming Interface">API</abbr> documentation ready around
2013.</p>
