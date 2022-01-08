<?php

use admin\controllers\TemplatesEditController as Controller;

class TemplatesEditTest extends \Codeception\Test\Unit
{
    public function testIndex(): void
    {
        $controller = new Controller;
        $content = $controller->actionIndex();
        self::assertIsString($content);
    }
}
