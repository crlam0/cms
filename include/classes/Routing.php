<?php

namespace Classes;

use Classes\MyGlobal;

final class Routing {
    private $routes = array();
    public $request_uri;
    
    public function __construct($request_uri) {
        global $SUBDIR;
        $this->routes = [];
        
        $this->addRoutesFromConfig('routes.global.php');
        $this->addRoutesFromConfig('routes.local.php');       
        
        if($SUBDIR !== '/') {
            $this->request_uri = str_replace($SUBDIR, '', $request_uri);
        } else {
            $this->request_uri = substr($request_uri, 1);
        } 
        
        if(strstr($this->request_uri,'modules/')) {
            $this->request_uri = str_replace('modules/', '', $this->request_uri);
        }

    }

    public function addRoutesFromConfig($file) {
        global $INC_DIR;
        if(file_exists($INC_DIR . 'config/' . $file)) {
            $this->routes = array_merge($this->routes, require $INC_DIR . 'config/' . $file);
        }
    }
    
    public function isIndexPage () {
        return !$this->request_uri or $this->request_uri==='' or $this->request_uri==='/';
    }
    
    public function hasGETParams () {
        return strpos($this->request_uri,'?')!==false;
    }

    public function proceedGETParams () {
        global $input;
        $get_param=substr($this->request_uri,strpos($this->request_uri,'?')+1);
        $this->request_uri = substr($this->request_uri,0,strpos($this->request_uri,'?'));
        if(strpos($get_param,'&')){
            $get_array = explode('&',$get_param);
        } else {
            $get_array[] = $get_param;
        }
        foreach ($get_array as $param) {
            if(strpos($param,'=')) {
                $param_array = explode('=',$param);
                $input[$param_array[0]] = MyGlobal::get('DB')->test_param($param_array[1]);
            } else {
                $input[$param] = true;
            }
        }
        unset($get_param, $get_array, $param, $param_array);
    }
    
    public function getFileName () {
        global $input;
        $file = false;
        foreach($this->routes as $title => $route) {
            $matches = [];
            if (preg_match('/'.$route['pattern'].'/', $this->request_uri, $matches) === 1) {
                add_to_debug("Match route '{$title}'");
                foreach($matches as $key => $value){
                    if($key==0){
                        $file=$route['file'];
                    } elseif (array_key_exists('params', $route)) {
                        $input[$route['params'][$key]]=htmlspecialchars($value);
                    }
                }
                break;
            }
        }
        return $file;
    }
    
}