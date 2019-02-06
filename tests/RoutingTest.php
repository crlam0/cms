<?php

use PHPUnit\Framework\TestCase;
use Classes\Routing;

require_once 'tests/bootstrap.php';

class RoutingTest extends TestCase
{
    private $Routing;
    
    public function setUp()
    {
        parent::setUp();
        $this->Routing = new Routing('/article/item/?test=1');
    }

    public function testHasGETParams() {
        $result = $this->Routing->hasGETParams();
        self::assertTrue($result);
    }
    
    public function testIsIndexPage() {
        $result = $this->Routing->isIndexPage();
        self::assertFalse($result);
    }
    
    public function testProceedGETParams() {
        global $input;
        $this->Routing->proceedGETParams();
        $result = ($input['test']==='1');
        self::assertTrue($result);
    }
    
    public function testGetFileName() {        
        global $input;
        $this->Routing->proceedGETParams();
        $result=$this->Routing->getFileName();
        self::assertEquals('modules/article/index.php', $result);
        self::assertEquals('item', $input['uri']);
    }
    
}

