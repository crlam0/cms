<?php

namespace Classes;

use Classes\App;

final class Routing {
    private $routes = [];
    public $request_uri;
    
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
    }

    public function addRoutesFromConfig($file) {
        if(file_exists(__DIR__ . '/../config/' . $file)) {
            $this->routes = array_merge($this->routes, require __DIR__ . '/../config/' . $file);
        }
    }
    
    public function isIndexPage () {
        return !$this->request_uri or $this->request_uri==='' or $this->request_uri==='index.php';
    }
    
    public function getFileName () {
        global $input;
        $file = false;
        foreach($this->routes as $title => $route) {
            $matches = [];
            if (preg_match('/'.$route['pattern'].'/', $this->request_uri, $matches) === 1) {
                App::debug("Match route '{$title}'");
                foreach($matches as $key => $value){
                    if($key==0){
                        $file=$route['file'];
                    } elseif (array_key_exists('params', $route)) {
                        $input[$route['params'][$key]]=App::$db->test_param($value);
                    }
                }
                break;
            }
        }
        return $file;
    }
    
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