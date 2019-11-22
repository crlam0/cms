<?php

namespace Classes;

class Controller {
    
    public $title = '';
    public $breadcrumbs = [];
    
    private function runMethod($methodName, $params) {
        if (method_exists($this, $methodName)) {
            $method = new \ReflectionMethod($this, $methodName);
            if ($method->isPublic() && $method->getName() === $methodName) {
                $params = array_values($params);
                return $this->$methodName(... $params);
            }
        }
        throw new \InvalidArgumentException('Method ' . $methodName . ' not found.');
    }
    
    public function execute($action, $params) {
        $method = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $action))));
        return $this->runMethod($method, $params);
    }
    
}
