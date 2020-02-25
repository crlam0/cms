<?php

namespace Classes;

use Classes\App;

final class Routing {
    /**
    * @var array All routes
    */
    private $routes = [];
    /**
    * @var string URI
    */
    public $request_uri;
    /**
    * @var string File to include (legacy)
    */
    public $file;
    /**
    * @var string Controller name
    */
    public $controller;
    /**
    * @var string Controller default action
    */
    public $action;
    /**
    * @var array Controller params
    */
    public $params = [];
    
    /**
     * Constructor
     *
     * @param string $request_uri URI from $_SERVER
     *
     */
    public function __construct($request_uri) {
        $this->routes = [];
        $this->request_uri = $request_uri;
        
        $this->addRoutesFromConfig('routes.global.php');
        $this->addRoutesFromConfig('routes.local.php');       
        
        if(App::$SUBDIR !== '/') {
            $this->request_uri = str_replace(App::$SUBDIR, '', $this->request_uri);
        }
        if(substr($this->request_uri,0,1) === '/') {
            $this->request_uri = substr($this->request_uri, 1);
        }
        if(strpos($this->request_uri,'?')!==false) {
            $this->request_uri = substr($this->request_uri,0,strpos($this->request_uri,'?'));
        }        
        if(strstr($this->request_uri,'modules/')) {
            $this->request_uri = str_replace('modules/', '', $this->request_uri);
        }
        $this->matchRoutes();
    }

    /**
     * Add routes from file
     *
     * @param string $file
     *
     */
    public function addRoutesFromConfig($file) {
        if(file_exists(__DIR__ . '/../config/' . $file)) {
            $this->routes = array_merge($this->routes, require __DIR__ . '/../config/' . $file);
        }
    }
    
    /**
     * Return true if index page
     *
     * @return boolean
     */
    public function isIndexPage () {
        return !$this->request_uri or $this->request_uri==='' or $this->request_uri==='index.php';
    }
    
    /**
     * Add found params to input
     *
     * @param string $route Found route
     * @param string $matches Found matches
     *
     */
    private function proceedInput ($route, $matches) {
        global $input;
        foreach($matches as $key => $value){
            if (array_key_exists('params', $route)) {
                $value = App::$db->test_param($value);
                $input[$route['params'][$key]] = $value;
                App::$input[$route['params'][$key]] = $value;
            }
        }
    }
    
    /**
     * Add found params to controller
     *
     * @param string $route Found route
     * @param string $matches Found matches
     *
     */
    private function proceedParams ($route, $matches) {
        foreach($matches as $key => $value){
            if (array_key_exists('params', $route)) {
                $this->params[$route['params'][$key]] = App::$db->test_param($value);
            }
        }
    }
    
    /**
     * Proceed found router
     *
     * @param string $route Found route
     * @param string $matches Found matches
     *
     */
    private function proceedMatches ($route, $matches) {        
        if(isset($route['file'])) {
            $this->file = $route['file'];
            $this->proceedInput($route, $matches);
        } elseif (isset($route['controller'])) {
            $this->controller = $route['controller'];            
            $this->proceedParams($route, $matches);
            if(array_key_exists('action', $route)) {
                $this->action = $route['action'];
            } else {
                $this->action = $this->getAction();
            }
        }
    }
    
    /**
     * Try to find route.
     *
     */
    private function matchRoutes () {
        foreach($this->routes as $title => $route) {
            $matches = [];
            if (preg_match('/'.$route['pattern'].'/', $this->request_uri, $matches) === 1) {
                App::debug("Match route '{$title}'");
                array_shift($matches);
                $this->proceedMatches($route, $matches);
                break;
            }
        }
    }
    
    /**
     * Return action from URI
     *
     * @return string
     */
    private function getAction () {
        $action='';
        if(strlen($this->request_uri) == 0) {
            $action='index';
        }
        if(strpos($this->request_uri, '/')) {
            if(end(explode('/',$this->request_uri)) == '') {
                $action='index';
            } else {
                $action = end(explode('/',$this->request_uri));
            }
        } else {
            $action = $this->request_uri;
        }
        if(strlen($action)) {
            return $action;
        } else {
            throw new \InvalidArgumentException('Cant find default action');
        }
    }
   
    /**
     * Return site part settings
     *
     * @return array
     */
    public function getPartArray () {
        $query = "SELECT * FROM parts WHERE '" . $this->request_uri . "' LIKE concat(uri,'%') AND title<>'default'";
        $part = App::$db->select_row($query, true);        
        if (!$part['id']) {
            $query = "SELECT * FROM parts WHERE title='default'";
            $part = App::$db->select_row($query, true);
        }
        return $part;        
    }
    
}