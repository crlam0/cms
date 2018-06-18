<?php

use PHPUnit\Framework\TestCase;

require_once 'tests/misc.php';
  
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
        $content=$this->Template->get_tpl_by_title('main.tpl');
        self::assertStringStartsWith('<!DOCTYPE html>', $content);
    }
    
    public function testSQLParse()            
    {
        $content=$this->Template->get_tpl_by_title('blog_post');
        self::assertStringStartsWith('<div class=post_wrapper>', $content);
    }
    
}

