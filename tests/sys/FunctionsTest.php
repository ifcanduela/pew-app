<?php

require 'functions.php';

class FunctionsTest extends PHPUnit_Framework_TestCase
{
    public function test_cfg()
    {
    	# set and retrieve keys and values
        $this->assertEquals('config is set!', cfg('is_config_set', 'config is set!'));
        $this->assertEquals('config is set!', cfg('is_config_set'));
        $this->assertEquals(array('is_config_set' => 'config is set!'), cfg(true));
        
        # non-existant  and invalid keys
        $this->assertNull(cfg('this does not exist'));
        $this->assertNull(cfg(false));
        $this->assertNull(cfg(12.0));
        
        # set and retrieve default values
        $this->assertEquals('default value set', cfg('key_without_value', null, 'default value set'));
        $this->assertEquals('default value set', cfg('key_without_value'));

        # set and retrieve normal values with default parameter
        $this->assertEquals(1234, cfg('value_1234', 1234));
        $this->assertEquals(1234, cfg('value_1234', null, 5678));
        $this->assertEquals(1234, cfg('value_1234'));
    }

    public function test_pr()
    {
        $array = array(1, 2, 3);
        $integer = '1234';
        $string = 'output string';
        
        ob_start();
        pr($array, $title = null);
        $result = ob_get_contents();
        ob_end_clean();
        
        $this->assertEquals("Array\n(\n    [0] => 1\n    [1] => 2\n    [2] => 3\n)\n", $result);
        
        ob_start();
        pr(12, $title = 'Twelve');
        $result = ob_get_contents();
        ob_end_clean();
        
        $this->assertEquals("Twelve: 12", $result);
    }

    public function test_pew_exit()
    {
        $this->markTestSkipped("This function aborts the current script. It should be modified to make it useful.");
    }

    public function test_get_execution_time()
    {
        $this->assertEquals(0, get_execution_time());
        $this->assertNotEquals(0, get_execution_time());
        $this->assertNotEquals(0, get_execution_time(true));

        $t1 = get_execution_time();
        $t2 = get_execution_time();
        $this->assertGreaterThan($t1, $t2);
        $this->assertGreaterThan($t2, get_execution_time());
        $this->assertGreaterThan(get_execution_time(true), get_execution_time());
    }

    public function test_sanitize()
    {
        $str = '; DELETE FROM \"users\"';
        $this->assertEquals('; DELETE FROM \\\\\"users\\\\\"', sanitize($str));
        
        $str = '12';
        $this->assertEquals('12', sanitize($str));
        
        $str = 12;
        $this->assertEquals(12, sanitize($str));
    }

    public function test_clean_array_data()
    {
        $array_data = array('', array('\\"; DELETE * from users'), '000234');
        $result = array('', array('"; DELETE * from users'), '000234');
        
        $this->assertEquals($result, clean_array_data($array_data));
    }

    public function testPew_clean_string()
    {
        $str = "\"; DELETE * FROM 'users'";
        
        $this->assertEquals('\&quot;; DELETE * FROM \&#039;users\&#039;', pew_clean_string($str));
    }

    public function test_deref()
    {
        function returns_array()
        {
            return array(1, 2, 3, 4, 5, 6);
        }
        
        $this->assertEquals(3, deref(returns_array(), 2));
        $this->assertNull(deref(returns_array(), 10));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_deref_throws_exception()
    {
        $return = deref(returns_array(), 'as', true);
    }

    public function test_array_reap()
    {
        $array = array(
            array(1, 2, 3, 4, 5),
            array('string1', 'string2', 'string3', 'str4' => 'string4'),
            array('uno' => 'one', 'dos' => 'two', 'tres' => 'three'),
            'PEW' => true
        );
        
        $result1 = array(1 => array('str4' => 'string4'), 2 => array('uno' => 'one', 'dos' => 'two', 'tres' => 'three'));
        $result2 = array(2 => array('uno' => 'one'));
        $result3 = array(array(1, 2, 3, 4, 5), array('string1', 'string2', 'string3'));
        $result4 = array('PEW' => true);
        $result5 = array(1 => array(2 => 'string3'));
        $this->assertEquals($result1, array_reap($array, '#:$'));
        $this->assertEquals($result2, array_reap($array, '#:uno'));
        $this->assertEquals($result3, array_reap($array, '#:#'));
        $this->assertEquals($result4, array_reap($array, '$'));
        $this->assertEquals($result5, array_reap($array, '1:2'));
        $this->assertNull(array_reap($array, true));
        $this->assertEquals(array(), array_reap((object) $array, '1:2'));
        
        $obj = new stdClass();
        $obj->prop = 1;
        
        $array2 = array(
            'obj' => $obj
        );
        
        $result6 = array('obj' => $obj);
        $this->assertEquals($result6, array_reap($array2, '$'), "Objects as array values are not converted");
    }

    public function test_array_flatten()
    {
        $array = array(array(1, 2, 3), 4, array ('five' => 5, 'six' => 6));
        $this->assertEquals(array(1, 2, 3, 4, 5, 6), array_flatten($array));
    }

    public function test_array_to_xml()
    {
        $array = array(
            'one' => array('test'),
            1 => array(1, 2, 3)
        );
        $xml = 'root';
        
        $this->assertEquals('', array_to_xml($array, $xml));
    }

    public function test_file_name_to_class_name()
    {
        $this->assertEquals('PewClassName', file_name_to_class_name('pew_class_name'));
        $this->assertEquals('PewclassName', file_name_to_class_name('pewclass_name'));
        $this->assertEquals('PEWClassName', file_name_to_class_name('p_e_w_class_name'));
        $this->assertEquals('Pewclassname', file_name_to_class_name('pewclassname'));
        $this->assertEquals('PewClassName', file_name_to_class_name('pew_class_name'));
        $this->assertEquals('Pewclassname', file_name_to_class_name('pewclassname'));
    }

    public function test_class_name_to_file_name()
    {
        $this->assertEquals('pew_class_name', class_name_to_file_name('PewClassName'));
        $this->assertEquals('pewclass_name', class_name_to_file_name('PewclassName'));
        $this->assertEquals('p_e_w_class_name', class_name_to_file_name('PEWClassName'));
        $this->assertEquals('pewclassname', class_name_to_file_name('Pewclassname'));
        $this->assertEquals('pew_class_name', class_name_to_file_name('pewClassName'));
        $this->assertEquals('pewclassname', class_name_to_file_name('pewclassname'));
    }

    public function testRedirect()
    {
        $this->markTestSkipped("I still don't know how to test HTTP redirection.");
    }

    public function test_check_dirs()
    {
        if (is_dir(TESTS_PATH . '/testFunc_check_dir')) {
            rmdir(TESTS_PATH . '/testFunc_check_dir');
        }
        $this->assertFalse(is_dir(TESTS_PATH . '/testFunc_check_dir'));
        $this->assertTrue(check_dirs(TESTS_PATH . '/testFunc_check_dir'));
        $this->assertTrue(is_dir(TESTS_PATH . '/testFunc_check_dir'));
        
        $this->assertFalse(check_dirs(''));

        rmdir(TESTS_PATH . '/testFunc_check_dir');
    }

    public function test_slugify()
    {
        $str = 'This is a slug';
        $this->assertEquals('this-is-a-slug', slugify($str));
        
        $str = 'This has strange characters: áprÑi\n.´t-çth$i#s';
        $this->assertEquals('this-has-strange-characters-print-this', slugify($str));
        
        $str = 'This is a slug';
        $this->assertEquals('this-is-a-slug', slugify($str));
        
        $str = 'This is a slug';
        $this->assertEquals('this-is-a-slug', slugify($str));
    }

    public function test_to_underscores()
    {
        $this->assertEquals('______', to_underscores('- _-- '));
        $this->assertEquals('My_Class_Name', to_underscores('My Class Name'));
        $this->assertEquals('my_class_name', to_underscores('my class name'));
        $this->assertEquals('My_Class\Name', to_underscores('My-Class\\Name'));
    }

    public function test_root()
    {
        defined('ROOT') or define('ROOT', '/var/www/pew_root' . DIRECTORY_SEPARATOR);
        $this->assertEquals('/var/www/pew_root' .  DIRECTORY_SEPARATOR, root('', false));
        $this->assertEquals('/var/www/pew_root' . DIRECTORY_SEPARATOR . 'subdir', root('subdir', false));
    }

    public function test_url()
    {
        defined('URL') or define('URL', '/pew_root/');
        
        $this->assertEquals('/pew_root/', url('', false));
        $this->assertEquals('/pew_root/example2', url('example2', false));
    }

    public function test_www()
    {
        defined('WWW') or define('WWW', '/pew_root/www/');

        $this->assertEquals('/pew_root/www/', www('', false));
        $this->assertEquals('/pew_root/www/example2', www('example2', false));
    }

    public function test_print_config()
    {
        defined('APP') or define('APP', '/var/www/pew_root/app' . DIRECTORY_SEPARATOR);
        defined('URL') or define('URL', '/pew_root');
        defined('WWW') or define('WWW', '/pew_root/www');
        defined('ROOT') or define('ROOT', '/var/www/pew_root' . DIRECTORY_SEPARATOR);
        defined('SYSTEM') or define('SYSTEM', '/var/www/pew_root/sys' . DIRECTORY_SEPARATOR);

        ob_start();
        print_config();
        $result = ob_get_contents();
        ob_end_clean();
        
        $this->assertTrue(is_string($result));
        
        $lines = explode(PHP_EOL, trim($result));
        $this->assertEquals(4, count($lines));
    }
    
    public function test_user()
    {
        if (!defined('USESESSION') || !defined('USEAUTH') || defined('STDIN')) {
            $this->markTestIncomplete();
        } else {
            $user = user();
            $this->assertFalse($user);
        }
    }
}