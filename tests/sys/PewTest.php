<?php

require_once 'pew.class.php';

class PewTest extends PHPUnit_Framework_TestCase
{

	public function setUp()
	{
		Pew::clean(true);
	}

	public function test_exists()
	{
		$this->assertFalse(Pew::exists('nothing'));
		$this->assertFalse(Pew::exists(Pew::CURRENT_REQUEST_CONTROLLER));

		Pew::set('test_exists', new ZipArchive());
		$this->assertTrue(Pew::exists('test_exists'));
	}

	public function test_set()
	{
        $obj1 = new stdClass();
        $xml = new SimpleXMLElement('<test></test>');

        $this->assertInstanceOf('stdClass', Pew::set('obj1', $obj1));
        $this->assertInstanceOf('SimpleXMLElement', Pew::set('xml', $xml));

        $this->assertTrue(Pew::exists('obj1'));
        $this->assertTrue(Pew::exists('xml'));

        $this->assertFalse(Pew::set('obj1', new StdClass()));

        $this->assertFalse(Pew::set('integer', 12));
        $this->assertFalse(Pew::set('float', 99.99999));
        $this->assertFalse(Pew::set('string', 'no strings allowed'));
        $this->assertFalse(Pew::set('array', array()));
        $this->assertFalse(Pew::set('null', null));
        $this->assertFalse(Pew::set('true', true));
        $this->assertFalse(Pew::set('false', false));
	}

	public function test_get()
	{
        $this->assertInstanceOf('ZipArchive', Pew::get('ZipArchive'));

        $t1 = Pew::set('test1', new stdClass);
        Pew::set('test2', new stdClass);
        Pew::set('test3', new stdClass);
        Pew::set('test4', $t1);

        $this->assertInstanceOf('stdClass', Pew::get('test1'));
        $this->assertInstanceOf('stdClass', Pew::get('test2'));
        $this->assertInstanceOf('stdClass', Pew::get('test3'));
        $this->assertInstanceOf('stdClass', Pew::get('test4'));

        $this->assertEquals(Pew::get('test1'), Pew::get('test2'));
	}

	public function test_register()
	{
		Pew::register('session', 'ZipArchive');
		$this->assertInstanceOf('ZipArchive', Pew::get_session());
	}

	public function test_get_request()
	{
		try {
			$this->assertFalse(Pew::get_request('controller/action/param'));
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->asserttrue(true);
		}
		
		define('DEFAULT_CONTROLLER', 'cnt');
		define('DEFAULT_ACTION', 'some_action');
		require 'pew_request.class.php';

		$pr = Pew::get_request('controller/action/param');

		$this->assertInstanceof('PewRequest', $pr);
		$this->assertEquals('controller', $pr->controller);
		$this->assertEquals('controller', $pr->controller);
	}

	/**
	 * @expectedException BadMethodCallException
	 */
	public function test_call_static_throws_exception()
	{
		$this->assertTrue(Pew::get_nothing());
	}
}
