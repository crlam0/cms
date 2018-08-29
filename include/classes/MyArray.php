<?php

namespace Classes;

class MyArray implements \ArrayAccess {
    private $container = array();

    public function __construct() {
        $this->container = [];
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        // return isset($this->container[$offset]);
        return array_key_exists($offset, $this->container);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function &offsetGet($offset) {
        // return isset($this->container[$offset]) ? $this->container[$offset] : '';
        if(isset($this->container[$offset])) {
            $data = & $this->container[$offset];
            return $data;
        } else {
            $tmp = null;
            $data = & $tmp;
            return $data;
        }
    }
    
    public function count() {
        return count($this->container);
    }
    
    public function merge($array) {
        return array_merge($this->container, $array);
    }

}

