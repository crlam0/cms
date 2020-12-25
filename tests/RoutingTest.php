<?php

use PHPUnit\Framework\TestCase;
use classes\Routing;
use classes\App;

class RoutingTest extends TestCase
{
    private $Routing;
    
    public function setUp() : void
    {
        parent::setUp();
        $this->Routing = new Routing('/article/item/?test=test');
        $this->Routing->matchRoutes();
    }
    
    public function testIsIndexPage() 
    {
        $result = $this->Routing->isIndexPage();
        self::assertFalse($result);
    }
    
    public function testController() 
    {        
        $result = $this->Routing->controller;
        self::assertEquals('modules\article\Controller', $result);
        self::assertEquals(['alias'=>'item'], $this->Routing->params);
    }
    
    public function testGetPartArray() 
    {        
        $result=$this->Routing->getPartArray();
        self::assertEquals('default', $result['title']);
    } 
}

