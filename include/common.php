<?php

require 'config/config.local.php';
require 'config/misc.php';

if(file_exists(__DIR__.'/config/misc.local.php')) {
    require_once __DIR__.'/config/misc.local.php';
}    

if(file_exists($DIR.'vendor/autoload.php')) {
    require_once $DIR.'vendor/autoload.php';
} else {
    die('Cant find autoloader');
}

use Classes\App;
use Classes\Routing;
use Classes\User;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

$App = new App($DIR, $SUBDIR);
$App->connectDB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
$App->loadSettings(__DIR__.'/config/settings.local.php');
$App->loadGlobals($_GET, $_POST, $_SERVER);
$App->addGlobals();
$App->debug('App created, arrays loaded');
unset($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);

if(App::$settings['debug']) {
    $whoops = new Run();
    $whoops->writeToOutput(true);
    $whoops->allowQuit(true);
    $PrettyPageHandler = new PrettyPageHandler();
    $PrettyPageHandler->addDataTable('DEBUG Array',  App::$DEBUG);
    $whoops->pushHandler($PrettyPageHandler);
    $whoops->register();
    $App->debug('Added exception handler');
}

require_once __DIR__.'/lib_sql.php';
require_once __DIR__.'/lib_messages.php';
require_once __DIR__.'/lib_templates.php';
require_once __DIR__.'/lib_functions.php';
require_once __DIR__.'/lib_url.php';
$App->debug('Library loaded');

App::$user = new User();

if(App::$server['SERVER_PROTOCOL']) {
    session_cache_limiter('nocache');
    session_name($SESSID);
    session_start();
    App::$user->authBySession($_SESSION);
} else {
    $DIR=dirname(dirname(__FILE__)) . '/';
}

if(App::$server['SERVER_PROTOCOL']) {
    require_once __DIR__.'/lib_stats.php';
    $App->debug('Stats added');
    if( !App::$user->id && $arr = App::$user->getRememberme($COOKIE_NAME)) {
        App::$user->authByArray($arr);
        list($_SESSION['UID'],$_SESSION['FLAGS']) = $arr;
        unset($arr);
    }
}

App::$routing = new Routing (App::$server['REQUEST_URI']);
if (App::$routing->hasGETParams()) {
    App::$routing->proceedGETParams();
}
$App->debug('Routing added');

$part = App::$routing->getPartArray();
if (!$part['id']) {
    my_msg('default_tpl_not_found');
    exit();
}
$App->set('tpl_default', $part['title']);

$App->debug('Part data loaded');

if(!App::$user->checkAccess($part['user_flag'])) {
    if (App::$user->id) {
        $content ='<h1 align=center>У вас нет соответствующих прав !</h1>';
        echo get_tpl_default([], null, $content);
    } else {
        $_SESSION['GO_TO_URI'] = $server['REQUEST_URI'];
        redirect(App::$SUBDIR . 'login/');
    }
    exit;
}
$App->debug('User flag checked');

$server['PHP_SELF_DIR']=dirname($server['PHP_SELF']).'/';

$content='';
$tags['INCLUDE_HEAD']='';
$tags['INCLUDE_CSS']='';
$tags['INCLUDE_JS']='';

$App->debug('common.php complete');
