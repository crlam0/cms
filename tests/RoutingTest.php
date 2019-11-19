<?php

use PHPUnit\Framework\TestCase;
use Classes\Routing;

class RoutingTest extends TestCase
{
    private $Routing;
    
    public function setUp()
    {
        parent::setUp();
        $this->Routing = new Routing('/article/item/?test=1');
    }

    public function testIsIndexPage() {
        $result = $this->Routing->isIndexPage();
        self::assertFalse($result);
    }
    
    public function testGetFileName() {        
        global $input;
        $result=$this->Routing->getFileName();
        self::assertEquals('modules/article/index.php', $result);
        self::assertEquals('item/', $input['uri']);
    }
    
    public function testGetPartArray() {        
        global $input;
        $result=$this->Routing->getPartArray();
        self::assertEquals('default', $result['title']);
    }    
}

