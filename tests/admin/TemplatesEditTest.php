<?php

use PHPUnit\Framework\TestCase;
use admin\controllers\TemplatesEditController as Controller;

class TemplatesEditTest extends TestCase
{
    public function testIndex(): void
    {
        $controller = new Controller;
        $content = $controller->actionIndex();
        self::assertIsString($content);
    }
}
