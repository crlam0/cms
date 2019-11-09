<?php

use PHPUnit\Framework\TestCase;
use Classes\Template;

class TemplateTest extends TestCase
{
    private $Template;
    
    public function setUp()
    {
        parent::setUp();
        $this->Template = new Template();
    }

    public function testFileParse()
    {
        $content=$this->Template->parse('tests/test.tpl');
        self::assertStringStartsWith('<!DOCTYPE html>', $content);
    }
    
    public function testSQLParse()            
    {
        $content=$this->Template->parse('user_login_promt');
        self::assertStringStartsWith('<div class="center-block" align="center">', $content);
    }
    
}

