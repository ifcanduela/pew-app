<?php

define('PEWPEWPEW', true);

include 'test.php';
include '../functions.php';
include '../app.class.php';

class AppTest extends App
{
    public $prefix = 'test_';
    public $tests = array(
        'get_segments_1',
        'get_segments_2',
        'get_segments_3',
        'get_segments_4',
        'get_segments_5',
    );
    
    function __construct()
    {
        
        foreach ($this->tests as $test) {
            $test_case = $this->prefix . $test;
            $result = $this->$test_case();
            echo "Result for test $test was " . ($result ? 'OK' : 'KO') . PHP_EOL;
        }
    }
    
    function test_get_segments_1()
    {
        $url = 'my_controller/my_action/1/sort:5';
        
        $result = $this->get_segments($url);
        
        return assertEquality($result['controller'], 'my_controller')
            && assertEquality($result['action'], 'my_action')
            && assertEquality($result['id'], 1)
            && assertEquivalence($result['named']['sort'], 5);
    }
    
    function test_get_segments_2()
    {
        $url = 'my_controller/my_action/sort/0';
        
        $result = $this->get_segments($url);
        
        return assertEquality($result['action'], 'my_action');
    }
    
    function test_get_segments_3()
    {
        $url = 'my_controller/my_action/sort/0';
        
        $result = $this->get_segments($url);
        
        return assertExists($result['id'])
            && assertExists($result['uri']);
    }

    function test_get_segments_4()
    {
        $url = 'test/get_segments/4/0';
        
        $result = $this->get_segments($url);
        
        return assertExists($result['named'])
            && assertEmpty($result['named'])
            && assertEquivalence($result['passed'][0], 4)
            && assertEquivalence($result['passed'][0], $result['numbered'][2]);
    }
    
    function test_get_segments_5()
    {
        $url = 'test/get_segments/<script>document.clear()</script>/0';
        
        $result = $this->get_segments($url);
        
        return assertEquality($result['passed'][0], 'document.clear()');
    }
}

new AppTest();
