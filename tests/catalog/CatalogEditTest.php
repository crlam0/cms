<?php

use PHPUnit\Framework\TestCase;

class CatalogEditTest extends TestCase
{
    public function testPartEditIndex(): void
    {
        $controller = new modules\catalog\controllers\PartEditController;
        $content = $controller->actionIndex();
        self::assertIsString($content);
    }
    
    public function testItemEditIndex(): void
    {
        $controller = new modules\catalog\controllers\ItemEditController;
        $content = $controller->actionIndex(0);
        self::assertIsString($content);
    }
}
