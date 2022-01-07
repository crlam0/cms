<?php

use classes\App;
use classes\Routing;
use classes\Sitemap;

class SitemapTest extends \Codeception\Test\Unit
{

    public function setUp() : void
    {
        parent::setUp();
        App::$routing = new Routing('');
    }

    public function testSitemap(): void
    {
        $Sitemap = new Sitemap();
        $Sitemap->build_pages_array(['article']);
        $result = $Sitemap->write(true);
        self::assertStringContainsString('article/', $result['output']);
        self::assertGreaterThan(1, $result['count']);
    }
}
