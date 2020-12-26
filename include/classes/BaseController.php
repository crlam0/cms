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
    * @var array Needed user flag
    */
    public $user_flag = '';
    
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
                return $method->invokeArgs($this, $params);
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
     * @param array $params Additioanal params
     *
     */
    public function redirect($url = '', $params = []) 
    {   
        redirect($this->getUrl($url, $params));
    }

    /**
     * Get action Url
     *
     * @param string $url Additioanal URL
     * @param array $params Additioanal params
     *
     */
    public function getUrl($url = '', $params = []) 
    {        
        if(count($params)) {
            $url .= '?';
            $first = true;
            foreach ($params as $param => $value) {
                if($first) {
                    $first = false;
                } else {
                    $url .= '&';
                }
                $url .= urlencode($param) . '=' . urlencode($value);
            }
        }
        return $this->base_url . $url;
    }
    
    /**
     * Render selected template
     *
     * @param string $template
     * @param array $tags
     *
     */
    public function render($template, $tags = [], $result = null) 
    {        
        return App::$template->parse($template, array_merge($tags, ['this' => $this]), $result);
    }
    
    
}
