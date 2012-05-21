<?php

define('DEFAULT_CONTROLLER', 'test');
define('DEFAULT_ACTION', 'index');
define('DEFAULT_LAYOUT', 'default');

define('OUTPUT_TYPE_HTML', 'html');

// could this dependency be removed in the future?
require_once 'pew_request.class.php';
require_once 'view.class.php';

class ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var View
     */
    protected $object;
    
    /**
     * @covers View::get_view_file
     * @todo Implement testGet_view_file().
     */
    public function testGet_view_file()
    {
        $request = $this->getMockBuilder('PewRequest')->disableOriginalConstructor()->getMock();
        $this->
        $view = new View($request);
        $view_file = $view->get_view_file();
        $this->assertEquals('test_controller/test_action.php', $view_file);
    }

    /**
     * @covers View::get_layout_file
     * @todo Implement testGet_layout_file().
     */
    public function testGet_layout_file() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers View::render
     * @todo Implement testRender().
     */
    public function testRender() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers View::render_html
     * @todo Implement testRender_html().
     */
    public function testRender_html() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers View::render_twig
     * @todo Implement testRender_twig().
     */
    public function testRender_twig() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers View::render_json
     * @todo Implement testRender_json().
     */
    public function testRender_json() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers View::render_xml
     * @todo Implement testRender_xml().
     */
    public function testRender_xml() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers View::exists
     * @todo Implement testExists().
     */
    public function testExists() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers View::element
     * @todo Implement testElement().
     */
    public function testElement() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}

?>
