<?php

use PHPUnit\Framework\TestCase;

use classes\CacheAdapter;

class CacheAdapterTest extends TestCase
{
    private $cache = null;

    public function setUp() : void
    {
        parent::setUp();
        $this->cache = new CacheAdapter('var/cache/misc/');
    }

    public function testSet(): void
    {
        $result = $this->cache->set('test', 'test');
        self::assertEquals('test', $result);
    }

    public function testHas(): void
    {
        $result = $this->cache->has('test');
        self::assertTrue($result);
    }

    public function testGet(): void
    {
        $result = $this->cache->get('test');
        self::assertEquals('test', $result);
    }

    public function testDelete(): void
    {
        $this->cache->delete('test');
        $result = $this->cache->has('test');
        self::assertFalse($result);
    }
}
