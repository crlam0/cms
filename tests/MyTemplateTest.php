<?php

use PHPUnit\Framework\TestCase;
use classes\MyTemplate;

class MyTemplateTest extends TestCase
{
    private $Blocks;
    private $MyTemplate;

    public function setUp() : void
    {
        parent::setUp();
        $this->MyTemplate = new MyTemplate;
    }

    public function testSimpleParse(): void
    {
        $content=$this->MyTemplate->parse('test=[%code%]', ['code'=>'123']);
        self::assertEquals('test=123' . "\n", $content);
    }

    public function testSQLParse(): void
    {
        $result=classes\App::$db->query("select login from users where login='admin'");
        $content=$this->MyTemplate->parse("[%loop_begin%]\n[%row(login)%]\n[%loop_end%]", [], $result);
        self::assertEquals('admin' . "\n", $content);
    }
}
