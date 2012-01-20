<?php

class Reference extends Pages
{
    public $index = array(
        array('index', 'Functions'),
        array('app', 'App class'),
        array('controller', 'Controller class'),
        array('model', 'Model class'),
        array('auth', 'Auth class'),
        array('session', 'Session class'),
        array('pew', 'Pew class'),
        array('log', 'Log class'),
    );
    
    function before_render()
    {
        //@to-do: Markdown
    }
}