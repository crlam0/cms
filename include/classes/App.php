<?php

namespace classes;
use classes\MyArray;

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
    * @var FileCache Cache object
    */
    public static $cache;
    
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
    * @var String Full path to App
    */
    public static $DIR;
    /**
    * @var String Subdir of App
    */
    public static $SUBDIR;
    /**
    * @var Array Debug array
    */
    public static $DEBUG_ARRAY;
    /**
    * @var Monolog\Logger Object of logger
    */    
    public static $logger;
    /**
    * @var Array Errors from validation etc.
    */    
    private static $errors = [];
    /**
    * @var Array Debug array
    */
    private static $data = [];
    
    /**
    * @var Array Array of denied words for input strings
    */
    private $DENIED_WORDS=['union','insert','update ','delete ','alter ','drop ','\$_[','<?php','<script','javascript'];
    
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
        static::$DEBUG_ARRAY[0] = microtime(true);
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
                static::$input[$key]=static::$db->testParam($value,$key);
            }
        }
        if(is_array($post)){
            static::$post = $post;
            foreach ($post as $key => $value){
                static::$input[$key]=static::$db->testParam($value,$key);
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
        $time = microtime(true) - static::$DEBUG_ARRAY[0];
        $time = sprintf('%.4F', $time);
        static::$DEBUG_ARRAY[] = $time . "\t " . $message;
        static::$logger->debug($message);
    }
    
    public static function getErrors () : array
    {
        return static::$errors;
    }

    public static function setErrors (array $errors) : void
    {
        static::$errors = $errors;
    }
    
    public static function addToErrors (string $error) : void
    {
        static::$errors[] = $error;
    }
    
    private function failedAuth() {
        if (static::$user->id) {
            static::debug('Failed auth, user ID: ' . static::$user->id . ' URL: ' . static::$routing->request_uri);
            return static::$message->get('error', [] ,'У вас нет соответствующих прав !');
        } else {
            $_SESSION['GO_TO_URI'] = static::$server['REQUEST_URI'];
            redirect(static::$SUBDIR . 'login/');
        }
        exit;
    }
    
    private function runController($controller_name, $action, $tags)
    {
        static::debug('Create controller "' . $controller_name . '" and run action "' . $action . '"');
        $controller = new $controller_name;
        try {            
            $controller->base_url = static::$routing->getBaseUrl();
            if(static::$user->checkAccess($controller->user_flag)) {
                $content = $controller->run($action, static::$routing->params);
                $tags['Header'] = $controller->title;
                header(App::$server['SERVER_PROTOCOL'] . ' 200 Ok', true, 200);
            } else {
                $content = $this->failedAuth();
                $tags['Header'] = 'Ошибка авторизации';
                header(App::$server['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);
            }           
            /* Fill tags for default template */
            $tags['breadcrumbs'] = array_merge($tags['breadcrumbs'], $controller->breadcrumbs);
            $tags = array_merge($tags, $controller->tags);
            echo static::$template->parse(static::get('tpl_default'), $tags, null, $content);
            exit;
        } catch (\Throwable $e) {
            static::debug('Exception: ' . $e->getMessage());
            static::debug('File: ' . $e->getFile() . ' (Line:' . $e->getLine().')');
            static::debug($e->getTraceAsString());
        }
    }
    
    public function run ($tags) : void 
    {
        $file = static::$routing->file;
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
        } else {
            static::debug('ERROR: empty controller name in routing.');
        }       
    }
    
}