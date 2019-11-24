<?php

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    private $Controller;
    
    public function setUp()
    {
        parent::setUp();
        $this->Controller = new Tests\MyTestController;
    }

    public function testRun() {        
        $result=$this->Controller->run('index', ['arg1'=>'test1','arg2'=>'test2']);
        self::assertEquals('test1<br />test2', $result);
        self::assertEquals('Test', $this->Controller->title);
    }
    
}
