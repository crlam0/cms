<?php

namespace classes;

class BaseController 
{
    
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
    
    /**
    * @var string Base URL for controller's views
    */
    public $base_url = '';
    
    /**
     * Set empty values for HTML blocks.
     *
     */
    public function __construct() 
    {
        $this->tags['INCLUDE_HEAD']='';
        $this->tags['INCLUDE_CSS']='';
        $this->tags['INCLUDE_JS']='';
    }
    
    /**
     * Check method and run it if exists
     *
     * @param string $methodName Method name
     * @param array $params Params array
     *
     * @return string Content
     */
    private function runMethod(string $methodName, array $params = []) 
    {
        if (method_exists($this, $methodName)) {
            $method = new \ReflectionMethod($this, $methodName);
            if ($method->isPublic() && $method->getName() === $methodName) {
                return $method->invokeArgs($this,$params);
            }
        }
        throw new \InvalidArgumentException('Method ' . $methodName . ' not found.');
    }
    
    /**
     * Run action with params
     *
     * @param string $action Action name
     * @param array $params Params array
     *
     * @return string Content
     */
    public function run(string $action, array $params = []) 
    {
        $method = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $action))));
        return $this->runMethod($method, $params);
    }
    
    /**
     * Redirect to self 
     *
     * @param string $url Additioanal URL
     *
     */
    public function redirect($url = '') {
        redirect($this->base_url . $url);
    }
    
}
