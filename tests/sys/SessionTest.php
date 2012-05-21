<?php

require_once 'session.class.php';

/**
 * All session-related tests must be run in separate processes due to
 * the "headers already sent" and "cookie already sent" issues.
 */
class SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $prfx;

    protected function setUp()
    {
        # The $prfx variable contains the session prefix in the $_SESSION array
        $this->prfx = basename(getcwd());
    }

    protected function tearDown()
    {
        
    }

    /**
     * @runInSeparateProcess
     */
    public function test_open()
    {
        $session = new Session(false);
        
        $this->assertFalse($session->is_open());
        $this->assertFalse(isset($_SESSION));

        $session->open();

        $this->assertTrue($session->is_open());
        $this->assertTrue(isset($_SESSION));
        $this->assertArrayHasKey($this->prfx, $_SESSION);

        $session->close();
    }

    /**
     * @runInSeparateProcess
     */
    public function test_close()
    {
        $session= new Session;

        $this->assertTrue($session->is_open());
        $this->assertTrue(isset($_SESSION));

        $session->close();
        
        $this->assertFalse($session->is_open());
        $this->assertEquals("", session_id());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_get_session_prefix()
    {
        $session = new Session;

        $this->assertEquals($this->prfx, $session->get_session_prefix());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_exists()
    {
        $session = new Session;
        
        $session->write('testExists0', 0);
        $session->write('testExistsFalse', false);
        $session->write('testExistsNull', null);
        $session->write('testExistsArray', array());
        
        $this->assertTrue($session->exists('testExists0'));
        $this->assertFalse($session->exists('testExists1'));
        $this->assertTrue($session->exists('testExistsFalse'));
        $this->assertTrue($session->exists('testExistsNull'));
        $this->assertTrue($session->exists('testExistsArray'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_write_and_read()
    {
        $session = new Session;
        
        $session->write('testWrite1', 1);
        $session->write('testWriteFalse', false);
        $session->write('testWriteString', 'String');
        
        $this->assertEquals(1, $_SESSION[$this->prfx]['testWrite1']);
        $this->assertEquals(false, $_SESSION[$this->prfx]['testWriteFalse']);
        $this->assertEquals('String', $_SESSION[$this->prfx]['testWriteString']);

        $session->close();
    }

    /**
     * @runInSeparateProcess
     */
    public function test_delete()
    {
        $session = new Session();

        $session->write('testDelete', 0);
        $this->assertTrue($session->exists('testDelete'));

        $session->delete('testDelete');
        $this->assertFalse($session->exists('testDelete'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_set_flash()
    {
        $session = new Session();
        $session->set_flash('testSet_Flash');
        $this->assertEquals('testSet_Flash', $_SESSION[$this->prfx][Session::FLASHDATA]);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_is_flash()
    {
        $session = new Session;
        $_SESSION[$this->prfx][Session::FLASHDATA] = 'testIs_Flash';
        $this->assertTrue($session->is_flash());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_get_flash()
    {
        $session = new Session();

        $_SESSION[$this->prfx][Session::FLASHDATA] = "test_get_flash";
        
        $this->assertEquals("test_get_flash", $session->get_flash());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_magic_set()
    {
        $session = new Session;
        
        $session->test_set = 'test_set';
        $this->assertEquals('test_set', $_SESSION[$this->prfx]['test_set']);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_magic_get()
    {
        $session = new Session();
        
        $_SESSION[$this->prfx]['test_get'] = true;
        $this->assertEquals(true, $session->test_get);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_magic_isset()
    {
        $session = new Session();
        
        $_SESSION[$this->prfx]['test_isset'] = 1234;
        $this->assertTrue(isset($session->test_isset));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_magic_unset()
    {
        $session = new Session();
        
        $_SESSION[$this->prfx]['test_unset'] = 1234;
        unset($session->test_unset);
        $this->assertFalse(isset($session->test_isset));
    }
}
