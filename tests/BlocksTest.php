<?php

use PHPUnit\Framework\TestCase;
use classes\Blocks;
use classes\App;
use classes\User;
use classes\Routing;

require_once 'bootstrap.php';

class BlocksTest extends TestCase
{
    private $Blocks;
    
    public function setUp() : void
    {
        parent::setUp();
        $this->Blocks = new Blocks();
        App::$user = new User();
        App::$routing = new Routing('');
    }

    public function testBlocksMenuMain()
    {
        $content=$this->Blocks->content('menu_main');
        self::assertStringContainsString('id="menu-main"', $content);
    }
    
}

