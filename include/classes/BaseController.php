<?php

namespace Classes;

class BaseController {
    
    /**
    * @var string Page title
    */
    public $title = '';
    /**
    * @var array Page breadcrumns
    */
    public $breadcrumbs = [];
    /**
    * @var array Additioans tags
    */
    public $tags = [];
    
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
    
    public function run($action, $params) {
        $method = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $action))));
        return $this->runMethod($method, $params);
    }
    
}
