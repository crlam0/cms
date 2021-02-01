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

    public function testFileParse(): void
    {
        $content=$this->Template->parse('tests/test.tpl');
        self::assertStringStartsWith('<!DOCTYPE html>', $content);
    }

    public function testSQLParse(): void
    {
        $content=$this->Template->parse('faq_list');
        self::assertStringStartsWith('<div id="faq">', $content);
    }
}
