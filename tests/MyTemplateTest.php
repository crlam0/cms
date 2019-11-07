<?php

use PHPUnit\Framework\TestCase;
use Classes\MyTemplate;

require_once 'tests/bootstrap.php';

class MyTemplateTest extends TestCase
{
    private $Blocks;
    private $MyTemplate;
    
    public function setUp()
    {
        parent::setUp();
        $this->MyTemplate = new MyTemplate;
    }

    public function testSimpleParse()
    {
        $content=$this->MyTemplate->parse('test=[%code%]',['code'=>'123']);
        self::assertEquals('test=123' . "\n", $content);
    }
    
    public function testSQLParse()            
    {
        $result=Classes\App::$db->query("select login from users where login='boot'");
        $content=$this->MyTemplate->parse("[%loop_begin%]\n[%row(login)%]\n[%loop_end%]",[],$result);
        self::assertEquals('boot' . "\n", $content);
    }
    
}

