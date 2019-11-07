<?php

use PHPUnit\Framework\TestCase;
use Classes\Template;

require_once 'tests/bootstrap.php';

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
        $content=$this->Template->get_by_title('tests/test.tpl');
        self::assertStringStartsWith('<!DOCTYPE html>', $content);
    }
    
    public function testSQLParse()            
    {
        $content=$this->Template->get_by_title('user_login_promt');
        self::assertStringStartsWith('<div class="center-block" align="center">', $content);
    }
    
}

