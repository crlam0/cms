<?php

namespace Classes;

class BaseController {
    
    /**
    * @var string Page title
    */
    public $title = '';
    /**
    * @var array Page breadcrumbs
    */
    public $breadcrumbs = [];
    /**
    * @var array Additional tags
    */
    public $tags = [];
    
    private function runMethod(string $methodName, array $params = []) {
        if (method_exists($this, $methodName)) {
            $method = new \ReflectionMethod($this, $methodName);
            if ($method->isPublic() && $method->getName() === $methodName) {
                return $method->invokeArgs($this,$params);
            }
        }
        throw new \InvalidArgumentException('Method ' . $methodName . ' not found.');
    }
    
    public function run(string $action, array $params = []) {
        $method = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $action))));
        return $this->runMethod($method, $params);
    }
    
}
