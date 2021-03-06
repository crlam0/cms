<?php

namespace classes;

class MyArray implements \ArrayAccess
{
    private $container = [];

    public function __construct()
    {
        $this->container = [];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        // return isset($this->container[$offset]);
        return array_key_exists($offset, $this->container);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function &offsetGet($offset)
    {
        // return isset($this->container[$offset]) ? $this->container[$offset] : '';
        if (isset($this->container[$offset])) {
            $data = & $this->container[$offset];
            return $data;
        } else {
            $tmp = null;
            $data = & $tmp;
            return $data;
        }
    }

    public function count() : int
    {
        return count($this->container);
    }

    public function merge($array) : array
    {
        return array_merge($this->container, $array);
    }

    public function keyExists(string $key) : bool
    {
        return array_key_exists($key, $this->container);
    }
}
