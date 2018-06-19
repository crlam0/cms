<?php

use PHPUnit\Framework\TestCase;
use Classes\Blocks;
use Classes\MyTemplate;

require_once 'tests/bootstrap.php';

class MyTemplateTest extends TestCase
{
    private $Blocks;
    private $MyTemplate;
    
    public function setUp()
    {
        parent::setUp();
        $this->Blocks = new Blocks();
        $this->MyTemplate = new Classes\MyTemplate($this->Blocks);
    }

    public function testSimpleParse()
    {
        $content=$this->MyTemplate->parse('test=[%code%]',['code'=>'123']);
        self::assertEquals('test=123' . "\n", $content);
    }
    
    public function testSQLParse()            
    {
        $result=my_query('select login from users where id=7');
        $content=$this->MyTemplate->parse("[%loop_begin%]\n[%row(login)%]\n[%loop_end%]",[],$result);
        self::assertEquals('boot' . "\n", $content);
    }
    
}

