<?php

namespace classes;

use classes\MyArray;
use Cake\Log\Log;

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
    * @var Session Session implementation from Yii2 framework
    */
    public static $session;
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
    * @var Array Errors from validation etc.
    */
    private static $errors = [];
    /**
    * @var Array Container data
    */
    private static $data = [];
    /**
    * @var Array Assets data
    */
    private static $assets = [
        'header' => [],
        'head' => [],
        'css' => [],
        'js' => [],
    ];

    /**
    * @var Array Array of denied words for input strings
    */
    private $DENIED_WORDS=['union','insert','update ','delete ','alter ','drop ','\$_[','<?php','<script','javascript'];

    /**
     * Load settings to App
     *
     * @param string $dir Full path to App
     * @param string $subdir Subdir of App
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
     * @return void
     */
    public function setDB($db): void
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
        if (is_array($server)) {
            foreach ($server as $key => $value) {
                static::$server[$key]=$value;
            }
        }
        static::$input = new MyArray;
        if (is_array($get)) {
            static::$get = $get;
            foreach ($get as $key => $value) {
                static::$input[$key]=static::$db->testParam($value, $key);
            }
        }
        if (is_array($post)) {
            static::$post = $post;
            foreach ($post as $key => $value) {
                static::$input[$key]=static::$db->testParam($value, $key);
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
        if (is_array($settings_local)) {
            foreach ($settings_local as $key => $value) {
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
        if (file_exists($filename)) {
            $this->loadSettingsFromFile($filename);
        }
        $query='SELECT name,value FROM settings';
        $result=static::$db->query($query);
        while ($row = $result->fetch_array()) {
            static::$settings[$row['name']] = $row['value'];
        }
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
    public static function debug(string $message) : void
    {
        $time = microtime(true) - static::$DEBUG_ARRAY[0];
        $time = sprintf('%.4F', $time);
        static::$DEBUG_ARRAY[] = $time . "\t " . $message;
        Log::debug($message);
    }

    /**
     * Put message to DEBUG array
     *
     * @param string $message Message
     *
     */
    public static function error(string $message) : void
    {
        $time = microtime(true) - static::$DEBUG_ARRAY[0];
        $time = sprintf('%.4F', $time);
        static::$DEBUG_ARRAY[] = $time . "\t " . $message;
        Log::error($message);
    }

    /**
     * Set message to session
     *
     * @param string $type
     * @param string $message
     *
     * @return void
     */
    public static function setFlash(string $type, string $message): void
    {
        static::$session->setFlash($type, $message);
    }

    /**
     * Add message to session
     *
     * @param string $type
     * @param string $message
     *
     * @return void
     */
    public static function addFlash(string $type, string $message): void
    {
        static::$session->addFlash($type, $message);
    }

    /**
     * Get message from session
     *
     * @return array
     */
    public static function getFlash($key)
    {
        return static::$session->getFlash($key);
    }

    /**
     * Get messages from session
     *
     * @param array $result
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    private static function proceedFlashItem(&$result, $key, $value)
    {
        if (is_array($value)) {
            foreach ($value as $message) {
                $result[] = [
                    'type' => $key,
                    'message' => $message
                ];
            }
        } else {
            $result[] = [
                'type' => $key,
                'message' => $value
            ];
        }
    }

    /**
     * Get messages from session
     *
     * @return array
     */
    private static function getFlashes()
    {
        $result = [];
        foreach (static::$session->getAllFlashes(true) as $key => $value) {
            static::proceedFlashItem($result, $key, $value);
        }
        return $result;
    }

    /**
     * Set errors[]
     *
     * @param string $error
     */
    public static function setErrors(array $errors) : void
    {
        static::$errors = $errors;
    }

    /**
     * Add message to errors[]
     *
     * @param string $error
     */
    public static function addToErrors(string $error) : void
    {
        static::$errors[] = $error;
    }

    /**
     * Get errors[]
     *
     * @return array
     */
    public static function getErrors() : array
    {
        return static::$errors;
    }

    /**
     * Add item to $assets
     *
     * @param string $type
     * @param string $value
     *
     */
    public static function addAsset(string $type, string $value) : void
    {
        static::$assets[$type][] = $value;
    }

    /**
     * @return string
     */
    private function failedAuth()
    {
        if (static::$user->id) {
            static::debug('Failed auth, user ID: ' . static::$user->id . ' URL: ' . static::$routing->request_uri);
            return static::$message->getError('У вас нет соответствующих прав !');
        } else {
            static::$session['GO_TO_URI'] = static::$server['REQUEST_URI'];
            redirect(static::$SUBDIR . 'login/');
        }
        exit;
    }

    public static function sendResult(string $content, array $tags = [], int $code = 200): void
    {
        switch ($code) {
            case 200:
                $http_message = ' 200 Ok';
                break;
            case 301:
                $http_message = ' 301 Moved Permanently';
                break;
            case 302:
                $http_message = ' 302 Found';
                break;
            case 307:
                $http_message = ' 307 Temporary Redirect';
                break;
            case 403:
                $http_message = ' 403 Forbidden';
                break;
            case 500:
                $http_message = ' 500 Internal server error';
                break;
            default:
                $http_message = ' 404 Not found';
                break;
        }
        header(static::$server['SERVER_PROTOCOL'] . $http_message, true, $code);
        $tags['flash'] = static::getFlashes();
        $tags['errors'] = static::getErrors();
        echo static::$template->parse(static::get('tpl_default'), $tags, null, $content);
        exit;
    }

    private function getContent(object $controller, $content, array $tags): void
    {
        if (is_array($content)) {
            echo json_encode($content);
            exit();
        }
        $tags['Header'] = $controller->title;
        $tags['breadcrumbs'] = array_merge($tags['breadcrumbs'], $controller->breadcrumbs);
        $tags = \array_merge($tags, $controller->tags);
        foreach (static::$assets['header'] as $value) {
            header($value);
        }
        foreach (static::$assets['head'] as $value) {
            $tags['INCLUDE_HEAD'] .= $value . PHP_EOL;
        }
        foreach (static::$assets['css'] as $value) {
            $tags['INCLUDE_HEAD'] .= '<link href="' . App::$SUBDIR . $value . '" rel="stylesheet" />' . PHP_EOL;
        }
        foreach (static::$assets['js'] as $value) {
            $tags['INCLUDE_JS'] .= '<script src="' . App::$SUBDIR . $value . '"></script>' . PHP_EOL;
        }
        static::sendResult($content, $tags, 200);
    }

    private function logErrorResult($e)
    {
        static::error('Exception: ' . $e->getMessage());
        static::error('File: ' . $e->getFile() . ' (Line:' . $e->getLine().')');
        static::error($e->getTraceAsString());
    }

    /**
     * Run cotroller found in routing
     *
     * @param string $controller_name
     * @param string $action
     * @param array $tags
     *
     */
    private function runController(string $controller_name, string $action, array $tags = [])
    {
        static::debug('Create controller "' . $controller_name . '" and run action "' . $action . '"');
        $controller = new $controller_name;
        try {
            $controller->base_url = static::$routing->getBaseUrl();
            if (static::$user->checkAccess($controller->user_flag)) {
                $content = $controller->run($action, static::$routing->params);
            } else {
                $content = $this->failedAuth();
                $tags['Header'] = 'Ошибка авторизации';
                $this->sendResult($content, $tags, 403);
            }
            return $this->getContent($controller, $content, $tags);
        } catch (\BadMethodCallException $e) {
            $this->logErrorResult($e);
            $tags['Header'] = '';
            static::sendResult(static::$message->get('file_not_found', ['file_name' => static::$routing->request_uri]), $tags, 404);
        } catch (\Throwable $e) {
            $this->logErrorResult($e);
            $tags['Header'] = '';
            static::sendResult(static::$message->getError('Внутренние неполадки, приносим свои извинения.'), $tags, 500);
        }
    }

    /**
     * Run cotroller or include file found in routing
     *
     * @param array $tags
     *
     * @return void
     */
    public function run(array $tags = []): void
    {
        $file = static::$routing->file;
        if ($file && is_file(static::$DIR . $file)) {
            $server['PHP_SELF'] = static::$SUBDIR.$file;
            $server['PHP_SELF_DIR'] = static::$SUBDIR.dirname($file) . '/';
            require static::$DIR . $file;
            exit;
        }

        $controller_name = static::$routing->controller;
        if (strlen($controller_name)) {
            if (class_exists($controller_name)) {
                $this->runController($controller_name, static::$routing->action, $tags);
            } else {
                static::error('ERROR: Controller "' . $controller_name . '" not exists !"');
            }
        } else {
            static::debug('ERROR: empty controller name in routing.');
        }
    }
}
