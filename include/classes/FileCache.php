<?php
namespace classes;

use classes\App;

/**
 * Basic Cache
 *
 * @author BooT
 */
class FileCache {
    
    private $cache_path;
    
    public function __construct(string $cache_path)
    {
        $this->cache_path = App::$DIR . $cache_path;
    }
    
    private function getFileName($key) {
        $hash = hash('sha256', $key);
        return $this->cache_path . $this->directory.$hash[0].$hash[1].'/'.$hash;
    }
    
    public function get($key) : string
    {
        if($this->has($key)) {
            return file_get_contents($this->getFileName($key));
        }
    }
    
    public function has($key) : bool
    {
        return file_exists($this->getFileName($key));
    }
    
    public function set($key, $data) {
        if(!$key){
            return false;
        }
        if($this->has($key)) {
            return true;
        }
        $file_name = $this->getFileName($key);
        if(!file_exists(dirname($file_name))) {
            if (!mkdir(dirname($file_name), 0755, true)) {
                App::$logger->error('Cant create dir ' . dirname($file_name));
                return false;
            }
        }
        if(!file_put_contents($file_name, $data)) {
            App::$logger->error('Cant write ' . $this->getFileName($key));
            return false;
        }
        return true;
    }
    
    public function delete($key) {
        if(!$this->has($key)) {
            return true;
        }
        if(!unlink($this->getFileName($key))) {
            App::$logger->error('Cant delete ' . $this->getFileName($key));
            return false;
        }
        return true;
    }
    
    public function clear() {
        del_tree($this->cache_path);
    }
}
