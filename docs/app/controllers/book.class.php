<?php

class Book extends Pages
{
    public $index = array(
        array('index', 'Introduction'),
        array('framework', 'Framework overview'),
        array('installation', 'Installation'),
        array('configuration', 'Configuration options'),
        array('controllers', 'Controllers'),
        array('actions', 'Actions'),
        array('models', 'Models'),
        array('views', 'Views'),
        array('layouts', 'Layouts'),
        array('elements', 'Elements'),
        array('libraries', 'Libraries'),
        array('tools', 'Useful tools'),
        array('xdebug', 'Xdebug'),
    );
    
    function before_render()
    {
        //@to-do: Markdown
    }
}
