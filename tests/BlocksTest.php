<?php

use PHPUnit\Framework\TestCase;
use Classes\Blocks;

require_once 'tests/bootstrap.php';

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
        self::assertStringStartsWith('<div id=menu>', $content);
    }
    
}

