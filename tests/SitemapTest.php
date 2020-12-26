<?php

use PHPUnit\Framework\TestCase;

use classes\App;
use classes\Routing;
use classes\Sitemap;

require_once 'include/lib_url.php';

class SitemapTest extends TestCase
{

    public function setUp() : void
    {
        parent::setUp();
        App::$routing = new Routing('');
    }

    public function testSitemap()
    {
        $Sitemap = new Sitemap();
        $Sitemap->build_pages_array(['article']);
        $result = $Sitemap->write(true);
        self::assertStringContainsString('article/', $result['output']);
        self::assertGreaterThan(1, $result['count']);        
    }
    
}

