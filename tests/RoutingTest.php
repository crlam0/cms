<?php

use PHPUnit\Framework\TestCase;
use Classes\Routing;
use Classes\App;

class RoutingTest extends TestCase
{
    private $Routing;
    
    public function setUp()
    {
        parent::setUp();
        $this->Routing = new Routing('/article/item/?test=1');
    }

    /*
    public function testIsIndexPage() {
        $result = $this->Routing->isIndexPage();
        self::assertFalse($result);
    }
    */
    public function testFilename() {        
        $result=$this->Routing->file;
        self::assertEquals('modules/article/index.php', $result);
        self::assertEquals('item/', App::$input['uri']);
    }
    /*
    public function testGetPartArray() {        
        $result=$this->Routing->getPartArray();
        self::assertEquals('default', $result['title']);
    } 
     * 
     */   
}

