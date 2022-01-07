<?php

use classes\Routing;
use classes\App;

class RoutingTest extends \Codeception\Test\Unit
{
    private $Routing;

    public function setUp() : void
    {
        parent::setUp();
        $this->Routing = new Routing('/faq/page1/?test=test');
        $this->Routing->matchRoutes();
    }

    public function testIsIndexPage(): void
    {
        $result = $this->Routing->isIndexPage();
        self::assertFalse($result);
    }

    public function testController(): void
    {
        $result = $this->Routing->controller;
        self::assertEquals('modules\misc\FAQController', $result);
        self::assertEquals(['page'=>'1'], $this->Routing->params);
    }

    public function testGetPartArray(): void
    {
        $result=$this->Routing->getPartArray();
        self::assertEquals('default', $result['title']);
    }
}
