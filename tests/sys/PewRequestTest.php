<?php

require_once 'pew_request.class.php';

class PewRequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers PewRequest::__construct
     */
    public function testConstruct()
    {
        # Simple constructor
        $req1 = new PewRequest();
        $this->assertEquals($req1->query_string, '');
        $this->assertEquals($req1->request_method, 'GET');
        $this->assertFalse($req1->get);
        $this->assertFalse($req1->post);
        $this->assertFalse($req1->files);
        $_GET = $_POST = $_FILES = array();
        
        # Constructor with parameter
        $req2 = new PewRequest('controller/action');
        $this->assertEquals($req2->query_string, 'controller/action');
        $this->assertFalse($req2->get);
        $this->assertFalse($req2->post);
        $this->assertFalse($req2->files);
        $_GET = $_POST = $_FILES = array();
        
        # Constructor with server QUERY_STRING
        $_SERVER['QUERY_STRING'] = 'query/string';
        $req3 = new PewRequest();
        $this->assertEquals($req3->query_string, 'query/string');
        $this->assertFalse($req3->get);
        $this->assertFalse($req3->post);
        $this->assertFalse($req3->files);
        $_GET = $_POST = $_FILES = array();
        
        # Construct with GET data
        $_GET['controller'] = 'get_controller';
        $_GET['action'] = 'get_action';
        $req4 = new PewRequest();
        $this->assertEquals($req4->get, array('controller' => 'get_controller', 'action' => 'get_action'));
        $this->assertFalse($req4->post);
        $this->assertFalse($req4->files);
        $_GET = $_POST = $_FILES = array();
        
        # Construct with POST data
        $_POST['username'] = 'username';
        $_POST['password'] = md5('password');
        $req5 = new PewRequest();
        $this->assertEquals($req5->request_method, 'POST');
        $this->assertFalse($req5->get);
        $this->assertEquals($req5->post, array('username' => 'username', 'password' => md5('password')));
        $this->assertFalse($req5->files);
        $_GET = $_POST = $_FILES = array();
        
        # Construct with FILES data
        $_FILES = array(
            0 => array(
                'filename' => 'tmp29387',
                'mime_type' => 'image/jpeg'
            ),
            1 => array(
                'filename' => 'tmp12362',
                'mime_type' => 'image/jpeg'
            )
        );
        $req6 = new PewRequest();
        $this->assertEquals($req6->request_method, 'POST');
        $this->assertFalse($req6->get);
        $this->assertFalse($req6->post);
        $this->assertEquals(count($req6->files), 2);
        $_GET = $_POST = $_FILES = array();
    }
    
    /**
     * @covers PewRequest::parse
     * @covers PewRequest::remap
     * @group parsing
     */
    public function testParseBasic()
    {
        $req = new PewRequest();
        
        $uri = 'testc/testa/2/param1';
        $uri = $req->remap($uri);
        
        $req->parse($uri);
        $this->assertEquals($req->controller, 'testc');
        $this->assertEquals($req->action, 'testa');
        $this->assertEquals($req->values, array('2', 'param1'));
        $this->assertFalse($req->files);
        $this->assertFalse($req->post);
    }
    
    /**
     * @covers PewRequest::parse
     * @covers PewRequest::set_default
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage No controller segment found in []
     * @group parsing
     */
    public function testParseWithoutControllerSegment()
    {
        $req = new PewRequest();
        
        # don't set default segments, expect exception
        $req->parse();
    }
    
    /**
     * @covers PewRequest::parse
     * @covers PewRequest::set_default
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage No action segment found in [controller]
     * @group parsing
     */
    public function testParseWithoutActionSegment()
    {
        $req = new PewRequest();
        
        # don't set default segments, expect exception
        $req->parse('controller');
    }

    /**
     * @covers PewRequest::parse
     * @covers PewRequest::set_default
     * @group parsing
     */
    public function testParseWithDefaultSegments()
    {
        $req = new PewRequest();
        
        # set default segments
        $req->set_default('controller', 'action');
        $req->parse('');
        
        $this->assertEquals('controller', $req->controller);
        $this->assertEquals('action', $req->action);
    }
    
    /**
     * @covers PewRequest::parse
     * @group parsing
     */
    public function testParseWithNames()
    {
        $req = new PewRequest();
        $req->parse('controller/action/name1:value1/name2:value2/');
        $this->assertEquals($req->controller, 'controller');
        $this->assertEquals($req->action, 'action');
        $this->assertEquals($req->values, array('value1', 'value2'));
        $this->assertEquals($req->named, array('name1' => 'value1', 'name2' => 'value2'));
    }

    /**
     * @covers PewRequest::parse
     * @covers PewRequest::add_route
     * @covers PewRequest::remap
     * @group parsing
     */
    public function testParseWithRoutes()
    {
        $req = new PewRequest();
        $req->reset(true);
        
        $uri = $req->remap('21');
        $this->assertEquals('21', $uri);
        
        $this->assertEquals(1, PewRequest::add_route('/controllern$/', 'controller/action'));
        
        $uri = $req->remap('21');
        $this->assertEquals($uri, '21');
        
        $this->assertEquals(2, PewRequest::add_route('/(\d+)$/', 'contr/number/$1'));
        
        $uri = $req->remap('21');
        $this->assertEquals($uri, 'contr/number/21');
        
        $req->parse($uri);
        $this->assertEquals($req->controller, 'contr');
        $this->assertEquals($req->action, 'number');
        $this->assertEquals($req->values, array('21'));
        $this->assertEquals($req->id, 21);
    }

    /**
     * @covers PewRequest::parse
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Action is forbidden: _action
     * @group parsing
     */
    public function testParsePrivateAction()
    {
        $req = new PewRequest();
        $req->reset(true);
        
        # Throws exception InvalidArgumentException
        $req->parse('controller/_action/1');
        $this->assertEquals('action', $req->action);
    }

    /**
     * @covers PewRequest::parse
     * @group parsing
     */
    public function testParseWithPrefixes()
    {
        $req = new PewRequest();
        $req->reset(true);
        
        $req->parse('controller/:action/1');
        $this->assertEquals(PewRequest::OUTPUT_TYPE_JSON, $req->output_type);
        $this->assertEquals('action', $req->action);
        
        $req->parse('controller/@action/1');
        $this->assertEquals(PewRequest::OUTPUT_TYPE_XML, $req->output_type);
        $this->assertEquals('action', $req->action);
    }
    
    /**
     * @covers PewRequest::segments
     */
    public function testSegments()
    {
        $uri = 'controller/action/param1/12/name1:value1';
        $_GET['url'] = $uri;
        $req = new PewRequest($uri);
        $req->parse($uri);
        $segments = $req->segments();
        
        $this->assertArrayHasKey('controller', $segments);
        $this->assertEquals('controller', $segments['controller']);
        $this->assertArrayHasKey('action', $segments);
        $this->assertEquals('action', $segments['action']);
        $this->assertArrayHasKey('id', $segments);
        $this->assertEquals(12, $segments['id']);
        $this->assertArrayHasKey('named', $segments);
        $this->assertEquals('value1', $segments['named']['name1']);
    }

    /**
     * @covers PewRequest::set_default
     */
    public function testSet_default() {
        $req = new PewRequest;
        $req->set_default('def_contrl', 'def_actn');
        $req->parse('');
        
        $this->assertEquals('def_contrl', $req->controller);
        $this->assertEquals('def_actn', $req->action);
    }

    /**
     * @covers PewRequest::reset
     * @covers PewRequest::add_route
     */
    public function testReset()
    {
        $req = new PewRequest;
        $req->parse('controller/action');
        $this->assertEquals($req->controller, 'controller');
        $this->assertEquals($req->action, 'action');
        
        $req->reset();
        $this->assertEquals($req->controller, '');
        $this->assertEquals($req->action, '');
        
        $this->assertNotEquals(0, PewRequest::add_route('/^$/', 'controller/action'));
        $this->assertNotEquals(1, PewRequest::add_route('/^controller$/', 'controller/action'));
        $req->reset(true);
        
        $this->assertEquals(1, PewRequest::add_route('//', 'controller'));
    }
}
