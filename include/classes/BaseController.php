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
     * @return array
     *
     * @psalm-return list<mixed>
     */
    private function prepareParams(\ReflectionMethod $reflection, array $params = []): array 
    {
        $pass = [];
        foreach ($reflection->getParameters() as $param) {
            /* @var $param ReflectionParameter */
            if (isset($params[$param->getName()])) {
                $pass[] = $params[$param->getName()];
            } else {
                $pass[] = $param->getDefaultValue();
            }
        }
        return $pass;
    }
    
    private function wrongParams(\ReflectionMethod $reflection, array $params = []) : void
    {
        App::error('Expected args: ' . implode(', ', $reflection->getParameters()));
        $result = ''; $i=0; $size = count($params);
        foreach ($params as $key => $value) {
            $result .= 'Parameter #' . $i  . ' [  '.gettype($value).' $' . $key . ' ]';
            $i++;
            if($i < $size) {
                $result .= ', ';
            }
        }
        App::error('Actually args: ' . $result);
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
            $reflection = new \ReflectionMethod($this, $methodName);
            if (!$reflection->isPublic()) {
                throw new \InvalidArgumentException('Method ' . $methodName . ' found but is private.');
            }
            try {
                return $reflection->invokeArgs($this, $this->prepareParams($reflection, $params));
            } catch (\Exception $e) {
                App::error('Exception: ' . $e->getMessage());
                App::error('File: ' . $e->getFile() . ' (Line:' . $e->getLine().')');
                App::error($e->getTraceAsString());
                $this->wrongParams($reflection, $params);
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
     * @return void
     */
    public function redirect(string $url = '', array $params = []): void 
    {   
        redirect($this->getUrl($url, $params));
    }

    /**
     * Get action Url
     *
     * @param string $url Additioanal URL
     * @param array $params Additioanal params
     *
     * @return string
     */
    public function getUrl(string $url = '', array $params = []): string 
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
     * @param \mysqli_result|null $result
     *
     * @return string
     */
    public function render(string $template, array $tags = [], ?\mysqli_result $result = null): string 
    {        
        return App::$template->parse($template, array_merge($tags, ['this' => $this]), $result);
    }
    
    
}
