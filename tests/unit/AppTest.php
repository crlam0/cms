<?php

use classes\App;

class AppTest extends \Codeception\Test\Unit
{
    private $App;

    public function setUp() : void
    {
        parent::setUp();
        $this->App = new App(App::$DIR, App::$SUBDIR);
    }

    public function tearDown() : void
    {
        unset($this->App);
    }

    public function testSetGet(): void
    {
        App::set('test', '123');
        self::assertEquals('123', App::get('test'));
    }

    public function testLoadInputData(): void
    {
        $this->App->loadInputData(['test' => 'get'], [], []);
        self::assertEquals('get', App::$get['test']);
        self::assertEquals('get', App::$input['test']);
        $this->App->loadInputData([], ['test' => 'post'], []);
        self::assertEquals('post', App::$post['test']);
        $this->App->loadInputData([], [], ['test' => 'server']);
        self::assertEquals('server', App::$server['test']);
    }
}
