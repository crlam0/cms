<?php

use PHPUnit\Framework\TestCase;
use Classes\App;

//require_once 'tests/bootstrap.php';

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
    
    public function testLoadGlobals()
    {
        $this->App->loadGlobals(['test' => 'result'], [], []);
        self::assertEquals('result', App::$get['test']);
    }
}

