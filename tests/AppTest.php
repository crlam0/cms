<?php

use PHPUnit\Framework\TestCase;
use Classes\App;

class AppTest extends TestCase
{
    
    private $App;
    
    public function setUp() {
        parent::setUp();
        $this->App = new App('test1','test2');
    }
    
    public function tearDown() {
        global $DIR, $SUBDIR;
        $this->App = new App($DIR, $SUBDIR);
    }            

    public function testSetGet()
    {
        App::set('test', '123' );
        self::assertEquals('123', App::get('test'));
    }
    
    public function testDIR()
    {
        self::assertEquals('test1', App::$DIR);
    }
    
    public function testSUBDIR()
    {
        self::assertEquals('test2', App::$SUBDIR);
    }
    
    public function testLoadInputData()
    {
        $this->App->loadInputData(['test' => 'get'], [], []);
        self::assertEquals('get', App::$get['test']);
        self::assertEquals('get', App::$input['test']);
        $this->App->loadInputData([], ['test' => 'post'], []);
        self::assertEquals('post', App::$post['test']);
        $this->App->loadInputData([], [], ['test' => 'server']);
        self::assertEquals('server', App::$server['test']);
    }
}

