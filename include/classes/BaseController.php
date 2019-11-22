<?php

namespace Classes;

class BaseController {
    
    public $title = '';
    public $breadcrumbs = [];
    
    private function runMethod($methodName, $params) {
        if (method_exists($this, $methodName)) {
            $method = new \ReflectionMethod($this, $methodName);
            if ($method->isPublic() && $method->getName() === $methodName) {
                if(is_array($params)){
                    $params = array_values($params);
                    return $this->$methodName(... $params);
                }
                return $this->$methodName();
            }
        }
        throw new \InvalidArgumentException('Method ' . $methodName . ' not found.');
    }
    
    public function execute($action, $params) {
        $method = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $action))));
        return $this->runMethod($method, $params);
    }
    
}
