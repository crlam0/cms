<?php
namespace classes;

use classes\App;
use Cake\Cache\Cache;

/**
 * Basic Cache
 *
 * @author BooT
 */
class CacheAdapter
{
    private $cache_path;

    public function __construct(string $cache_path)
    {
        $this->cache_path = App::$DIR . $cache_path;
        if (!Cache::getConfig('default')) {
            Cache::setConfig('default', [
                'className' => 'Cake\Cache\Engine\FileEngine',
                'duration' => '+1 week',
                'probability' => 100,
                'path' => $this->cache_path,
            ]);
        }
    }

    public function get($key) : string
    {
        return Cache::read($key);
    }

    public function has($key) : bool
    {
        return Cache::read($key) !== false;
    }

    public function set($key, $data): string
    {
        if (!Cache::write($key, $data)) {
            App::error('Cant write cache data in ' . $this->cache_path);
        }
        return $data;
    }

    public function delete($key): bool
    {
        return Cache::delete($key);
    }

    public function clear(): void
    {
        Cache::clear();
    }
}
