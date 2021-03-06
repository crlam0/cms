<?php

namespace classes;

use classes\App;

final class Routing
{
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
    * @var string Base URL for controller's views
    */
    private $controller_base_url = null;
    /**
    * @var array Controller param's
    */
    public $params = [];
    /**
    * @var array Functions for URL generate
    */
    private $get_url_functions = [];

    /**
     * Constructor
     *
     * @param string $request_uri URI from $_SERVER
     *
     */
    public function __construct(string $request_uri)
    {
        $this->routes = [];
        $this->request_uri = $request_uri;

        $this->addRoutesFromConfig('/../../local/routes.php');
        $this->addRoutesFromConfig('/../config/routes.php');

        if (App::$SUBDIR !== '/') {
            $this->request_uri = str_replace(App::$SUBDIR, '', $this->request_uri);
        }
        if (substr($this->request_uri, 0, 1) === '/') {
            $this->request_uri = substr($this->request_uri, 1);
        }
        if (strpos($this->request_uri, '?') !== false) {
            $this->request_uri = substr($this->request_uri, 0, strpos($this->request_uri, '?'));
        }
        if (strstr($this->request_uri, 'modules/')) {
            $this->request_uri = str_replace('modules/', '', $this->request_uri);
        }
    }

    /**
     * Add routes from file
     *
     * @param string $file
     *
     */
    public function addRoutesFromConfig(string $file) : void
    {
        if (file_exists(__DIR__ . $file)) {
            $this->routes = array_merge($this->routes, require __DIR__ . $file);
        }
    }

    /**
     * Add routes from file
     *
     * @param string $file
     *
     */
    public function addRoutes(array $routes) : void
    {
        $this->routes = array_merge($this->routes, $routes);
    }

    /**
     * Return true if index page
     *
     * @return boolean
     */
    public function isIndexPage() : bool
    {
        return !$this->request_uri or $this->request_uri==='' or $this->request_uri==='index.php';
    }

    /**
     * Add found params to input
     *
     * @param array $route
     * @param array $matches
     */
    private function proceedInput(array $route, array $matches) : void
    {
        global $input;
        foreach ($matches as $key => $value) {
            if (array_key_exists('params', $route)) {
                $value = App::$db->testParam($value);
                $input[$route['params'][$key]] = $value;
                App::$input[$route['params'][$key]] = $value;
            }
        }
    }

    /**
     * Add found params to controller
     *
     * @param array $route
     * @param array $matches
     */
    private function proceedParams(array $route, array $matches) : void
    {
        if (array_key_exists('params', $route)) {
            foreach ($matches as $key => $value) {
                $this->params[$route['params'][$key]] = App::$db->testParam($value);
            }
        }
        if (count(App::$get)) {
            foreach (App::$get as $key => $value) {
                $this->params[$key] = $value;
            }
        }
    }

    /**
     * Proceed found router
     *
     * @param array $route
     * @param array $matches
     */
    private function proceedMatches(array $route, array $matches) : void
    {
        if (isset($route['file'])) {
            $this->file = $route['file'];
            $this->proceedInput($route, $matches);
        } elseif (isset($route['controller'])) {
            $this->controller = $route['controller'];
            $this->proceedParams($route, $matches);
            if (array_key_exists('action', $route)) {
                $this->action = $route['action'];
            } else {
                $this->action = $this->getAction();
            }
            if (array_key_exists('base_url', $route)) {
                $this->controller_base_url = App::$SUBDIR . $route['base_url'];
            }
        } else {
            App::debug('Unknown route type: not file or controller.');
        }
    }

    /**
     * Try to find route.
     *
     * @return void
     */
    public function matchRoutes(): void
    {
        foreach ($this->routes as $title => $route) {
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
     * Return URL without action name.
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        if ($this->controller_base_url !== null) {
            return $this->controller_base_url;
        }
        if (substr($this->request_uri, strlen($this->request_uri) - 1, 1) !== '/') {
            return App::$SUBDIR . substr($this->request_uri, 0, strrpos($this->request_uri, '/') + 1);
        }
        return App::$SUBDIR . $this->request_uri;
    }

    /**
     * Return action from URI
     *
     * @return string
     */
    private function getAction()
    {
        if (strlen($this->request_uri) == 0) {
            return 'index';
        }
        if (strpos($this->request_uri, '/')) {
            $arr = explode('/', $this->request_uri);
            if (end($arr) == '') {
                return 'index';
            } else {
                return end($arr);
            }
        } else {
            throw new \InvalidArgumentException('Cant find default action');
        }
    }

    /**
     * Return site part settings
     *
     * @return array
     */
    public function getPartArray()
    {
        $query = "SELECT * FROM parts WHERE ? LIKE concat(uri,'%') AND title<>'default'";
        $part = App::$db->getRow($query, ['request_uri'=>$this->request_uri]);
        if (!$part) {
            $query = "SELECT * FROM parts WHERE title='default'";
            $part = App::$db->getRow($query);
        }
        return $part;
    }


    /**
     * Add function for getUrl
     *
     * @param string $target_type
     * @param callable $function
     *
     * @return void
     */
    public function addGetUrlFunction(string $target_type, callable $function) : void
    {
        $this->get_url_functions[$target_type] = $function;
    }

    public function getRoute(string $name)
    {
        if (array_key_exists($name, $this->routes)) {
            return $this->routes[$name];
        }
        return null;
    }

    /**
     * Get HREF for some item
     *
     * @param string $target_type
     * @param integer $id
     * @param array $row Row from SQL query
     *
     * @return string Result string
     */
    public function getUrl(string $target_type, int $id = null, array $row = []) : string
    {
        if (isset($this->get_url_functions[$target_type])) {
            $function = $this->get_url_functions[$target_type];
            return $function($id, $row);
        }
        switch ($target_type) {
            case '':
                return $row['href'];
            case 'link':
                return $row['href'];
            case 'catalog':
                return 'catalog/';
            default:
                App::debug('Unknown target type in getUrl: ' . $target_type);
                return '';
        }
    }
}
