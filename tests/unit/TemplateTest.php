<?php

use classes\Template;

class TemplateTest extends \Codeception\Test\Unit
{
    private $Template;

    public function setUp() : void
    {
        parent::setUp();
        $this->Template = new Template();
        // $this->Template->addPath('modules/misc/views/');
    }

    public function testSQLParse(): void
    {
        $content=$this->Template->parse('faq_list');
        self::assertStringStartsWith('<div id="faq">', $content);
    }
}
