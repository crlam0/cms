<?php

use PHPUnit\Framework\TestCase;
use classes\Sitemap;

require_once 'include/lib_url.php';

class SitemapTest extends TestCase
{

    public function testSitemap()
    {
        $Sitemap = new Sitemap();
        $Sitemap->build_pages_array(['article']);
        $result = $Sitemap->write(true);
        self::assertContains('article/', $result['output']);
        self::assertGreaterThan(1, $result['count']);        
    }
    
}

