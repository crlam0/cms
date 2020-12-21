<?php

use PHPUnit\Framework\TestCase;
use classes\Template;

class TemplateTest extends TestCase
{
    private $Template;
    
    public function setUp() : void
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
        $content=$this->Template->parse('article_items');
        self::assertStringStartsWith('<div id="articles_list">', $content);
    }
    
}

