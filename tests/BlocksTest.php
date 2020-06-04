<?php

use PHPUnit\Framework\TestCase;
use classes\Blocks;

require_once 'bootstrap.php';

class BlocksTest extends TestCase
{
    private $Blocks;
    
    public function setUp()
    {
        parent::setUp();
        $this->Blocks = new Blocks();
    }

    public function testBlocksMenuMain()
    {
        $content=$this->Blocks->content('menu_main');
        self::assertContains('id="menu-main"', $content);
    }
    
}

