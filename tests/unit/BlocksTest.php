<?php

use classes\Blocks;
use classes\App;
use classes\User;
use classes\Routing;

class BlocksTest extends \Codeception\Test\Unit
{
    private $Blocks;

    public function setUp() : void
    {
        parent::setUp();
        $this->Blocks = new Blocks();
        App::$user = new User();
        App::$user->authByArray(['id'=>10, 'flags'=>'active']);
        App::$routing = new Routing('');
    }

    public function testBlocksMenuMain(): void
    {
        $content = $this->Blocks->content('menu_main');
        self::assertStringContainsString('id="menu-main"', $content);
    }
}
