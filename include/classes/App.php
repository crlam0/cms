<?php

namespace classes;
use classes\MyArray;
use classes\SQLHelper;

class App 
{
    /**
    * @var debug Use or not debug routines
    */
    public static $debug = false;
    /**
    * @var DB Database object
    */
    public static $db;
    /**
    * @var Routing Routing object
    */
    public static $routing;
    /**
    * @var User User object
    */
    public static $user;
    /**
    * @var Template Template object
    */
    public static $template;
    /**
    * @var Message Message object
    */
    public static $message;
    
    /**
    * @var Array Raw data from _GET
    */
    public static $get;
    /**
    * @var Array Raw data from _POST
    */
    public static $post;
    /**
    * @var Array Checked data from _GET and _POST
    */
    public static $input;
    /**
    * @var Array Data from _SERVER
    */
    public static $server;
    /**
    * @var Array Settings array from file and database
    */
    public static $settings;
    /**
    * @var Array Full path to App
    */
    public static $DIR;
    /**
    * @var Array Subdir of App
    */
    public static $SUBDIR;
    /**
    * @var Array Debug array
    */
    public static $DEBUG;
    

    /**
    * @var Array Debug array
    */
    private static $data = array();
    /**
    * @var Array Array of denied words for input strings
    */
    private $DENIED_WORDS=array('union','insert','update ','delete ','alter ','drop ','\$_[','<?php','<script','javascript');
    
    /**
     * Load settings to App
     *
     * @param string $dir Full path to App
     * @param array $subdir Subdir of App
     *
    */   
    public function __construct(string $dir, string $subdir = '/')
    {
        static::$DIR = $dir;
        static::$SUBDIR = $subdir;
        static::set('DIR', $dir);
        static::set('SUBDIR', $subdir);
        static::$DEBUG[0] = microtime(true);
    }
    
    /**
     * Set Database object
     *
     * @param object $db
     *
    */    
    public function setDB($db)
    {
        static::$db = $db;
    }

    /**
     * Load settings from arrays
     *
     * @param array $get _GET array
     * @param array $post _POST array
     * @param array $server _SERVER array
     *
    */    
    public function loadInputData(array $get, array $post, array $server) : void
    {
        static::$server = new MyArray;
        if(is_array($server)){
            foreach ($server as $key => $value){
                static::$server[$key]=$value;
            }
        }
        static::$input = new MyArray;
        if(is_array($get)){
            static::$get = $get;
            foreach ($get as $key => $value){
                static::$input[$key]=static::$db->test_param($value,$key);
            }
        }
        if(is_array($post)){
            static::$post = $post;
            foreach ($post as $key => $value){
                static::$input[$key]=static::$db->test_param($value,$key);
            }
        }
    }
    
    /**
     * Load settings from file
     *
     * @param string $filename Config filename
     *
    */    
    private function loadSettingsFromFile(string $filename) : void
    {        
        $settings_local=(require $filename);
        if(is_array($settings_local)) {
            foreach ($settings_local as $key => $value){
                static::$settings[$key]=$value;
            }
        }    
    }
    
    /**
     * Load settings from file (if exists) and database
     *
     * @param string $filename Config filename
     *
    */
    public function loadSettings(string $filename) : void
    {
        static::$settings = new MyArray;
        if(file_exists($filename)) {
            $this->loadSettingsFromFile($filename);
        }
        $query='SELECT name,value FROM settings';
        $result=static::$db->query($query);
        while ($row = $result->fetch_array()) {
            static::$settings[$row['name']] = $row['value'];
        }        
    }    
    
    /**
     * Put data to global arrays
     *
     */
    public function addGlobals() : void
    {
        global $input, $server, $settings, $mysqli;
        $input = static::$input;
        $server = static::$server;
        $settings = static::$settings;
        $mysqli = static::$db->mysqli;
    }    
    
    /**
     * Get value from container.
     *
     * @param string $key Key name
     *
     * @return string Output value
     */
    public static function get(string $key) 
    {
        return (isset(static::$data[$key]) ? static::$data[$key] : null);
    }

    /**
     * Set value to container.
     *
     * @param string $key Key name
     * @param string $value Key value
     *
     */
    public static function set(string $key, $value) : void 
    {
        static::$data[$key] = $value;
    }

    /**
     * Check value in container.
     *
     * @param string $key Key name
     *
     * @return boolean True if found
     */
    public static function has(string $key) : bool
    {
        return isset(static::$data[$key]);
    }
    
    /**
     * Put message to DEBUG array
     *
     * @param string $message Message
     *
     */
    public static function debug (string $message) : void 
    {
        $time = microtime(true) - static::$DEBUG[0];
        $time = sprintf('%.4F', $time);
        static::$DEBUG[] = $time . "\t " . $message;
    }
    
    private function runController($controller_name, $action, $tags)
    {
        static::debug('Create controller "' . $controller_name . '" and run action "' . $action . '"');
        $controller = new $controller_name;
        try {

            $content = $controller->run($action, static::$routing->params);
            /* Fill tags for default template */
            $tags['Header'] = $controller->title;
            $tags['nav_array'] = array_merge($tags['nav_array'], $controller->breadcrumbs);
            $tags = array_merge($tags, $controller->tags);
            header(App::$server['SERVER_PROTOCOL'] . ' 200 Ok', true, 200);
            echo static::$template->parse(static::get('tpl_default'), $tags, null, $content);
            exit;
        } catch (Exception $e) {
            static::debug('Exception: ' . $e->getMessage());
            static::debug('File: ' . $e->getFile() . ' (Line:' . $e->getLine().')');
            static::debug($e->getTraceAsString());
            /* But show 404 error */
        }        
    }
    
    public function run ($tags) : void 
    {
        $file=static::$routing->file;
        if($file && is_file(static::$DIR . $file)) {
            $server['PHP_SELF'] = static::$SUBDIR.$file;
            $server['PHP_SELF_DIR'] = static::$SUBDIR.dirname($file) . '/';
            require static::$DIR . $file;
            exit;
        } 
        
        $controller_name = static::$routing->controller;
        if(strlen($controller_name)) {
            if(class_exists($controller_name)) {
                $this->runController($controller_name, static::$routing->action, $tags);
            } else {
                static::debug('Controller "' . $controller_name . '" not exists !"');
            }
        }        
    }
    
}