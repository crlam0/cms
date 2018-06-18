<?php

use PHPUnit\Framework\TestCase;
use Classes\Sitemap;

require_once 'tests/require.php';

class SitemapTest extends TestCase
{

    public function testSitemap()
    {
        $Sitemap = new Sitemap();
        $Sitemap->build_pages_array(['article']);
        $result = $Sitemap->write();
        self::assertContains('article/', $result['output']);
        self::assertGreaterThan(1, $result['count']);        
    }
    
}

