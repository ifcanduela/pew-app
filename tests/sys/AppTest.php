<?php

require_once 'config.php';
require_once 'functions.php';
require_once 'app.class.php';

class AppTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var App
     */
    protected $app;

    protected function setUp()
    {
        $this->app = new App;
    }

    protected function tearDown()
    {
        $this->app = null;
    }

    /**
     * @todo Implement test_app_run().
     */
    public function test_app_run()
    {
        $this->markTestIncomplete("The App::run() method is untestable for the moment.");
        
        /*
         * App::run() requires class Pew, which in turn requires sys/config, 
         * which in turn requires app/config, which in turn...
         */
        //$this->app->run();
    }
}
