<?php

namespace Classes;
use Classes\MyArray;
use Classes\SQLHelper;

final class App {
    /**
    * @var SQLHelper Database object
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
    public function __construct(string $dir, string $subdir) {
        static::$DIR = $dir;
        static::$SUBDIR = $subdir;
        static::set('DIR', $dir);
        static::set('SUBDIR', $subdir);
        static::$DEBUG[0] = microtime(true);
    }

    /**
     * Load settings from arrays
     *
     * @param array $get _GET array
     * @param array $post _POST array
     * @param array $server _SERVER array
     *
    */    
    public function loadGlobals(array $get, array $post, array $server) {
        static::$input = new MyArray;
        static::$server = new MyArray;
        if(is_array($get)){
            static::$get = $get;
            foreach ($get as $key => $value){
                static::$input[$key]=$this->test_param($value,$key);
            }
        }
        if(is_array($post)){
            static::$post = $post;
            foreach ($post as $key => $value){
                static::$input[$key]=$this->test_param($value,$key);
            }
        }
        if(is_array($server)){
            foreach ($server as $key => $value){
                static::$server[$key]=$value;
            }
        }
    }
    
    /**
     * Load settings from file
     *
     * @param string $filename Config filename
     *
    */    
    private function loadSettingsFromFile($filename){        
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
    public function loadSettings($filename) {
        static::$settings = new MyArray;
        if(file_exists($filename)) {
            $this->loadSettingsFromFile($filename);
        }
        $query='SELECT name,value FROM settings';
        $result=static::$db->query($query,true);
        while ($row = $result->fetch_array()) {
            static::$settings[$row['name']] = $row['value'];
        }        
    }    
    
    /**
     * Test field parameter for deny SQL injections
     *
     * @param string $host Host
     * @param string $user Username
     * @param string $passwd Password
     * @param string $dbname DB
     *
     * @return string Output string
     */
    public function connectDB($host, $user, $passwd, $dbname) {
        static::$db = new SQLHelper($host, $user, $passwd, $dbname);
    }

    /**
     * Put data to global arrays
     *
     */
    public function addGlobals() {
        global $input, $server, $settings, $mysqli;
        $input = static::$input;
        $server = static::$server;
        $settings = static::$settings;
        $mysqli = static::$db->mysqli;
    }    
    
    /**
     * Test field parameter for deny SQL injections
     *
     * @param string $sql Input string
     *
     * @return string Output string
     */
    public function test_param($str,$param='') {
        if (is_array($str)) {
            foreach ($str as $key => $value) {
                $str[$key]=$this->test_param($value);
            }
            return $str;
        }    
        if(!strstr(static::$server['PHP_SELF'], 'admin/')) {
            $str=htmlspecialchars($str);            
        }
        $str = static::$db->escape_string($str);        
        foreach($this->DENIED_WORDS as $word) {
            if(stristr($str, $word)){
                header(static::$server['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
                exit();
            }
        }
        return $str;
    }
    
    /**
     * Get value from container.
     *
     * @param string $key Key name
     *
     * @return string Output value
     */
    public static function get($key) {
        return (isset(static::$data[$key]) ? static::$data[$key] : null);
    }

    /**
     * Set value to container.
     *
     * @param string $key Key name
     * @param string $value Key value
     *
     */
    public static function set($key, $value) {
        static::$data[$key] = $value;
    }

    /**
     * Check value in container.
     *
     * @param string $key Key name
     *
     * @return boolean True if found
     */
    public static function has($key) {
        return isset(static::$data[$key]);
    }
    
    /**
     * Put message to DEBUG array
     *
     * @param string $message Message
     *
     */
    public static function debug ($message) {
        $time = microtime(true) - static::$DEBUG[0];
        $time = sprintf('%.4F', $time);
        static::$DEBUG[] = $time . "\t " . $message;
    }
    
}