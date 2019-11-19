<?php

require __DIR__.'/config/config.local.php';
require __DIR__.'/config/misc.php';

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
use Classes\Template;
use Classes\Message;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

$App = new App($DIR, $SUBDIR);
$App->connectDB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
$App->loadSettings(__DIR__.'/config/settings.local.php');
$App->loadInputData($_GET, $_POST, $_SERVER);
$App->addGlobals();
$App->debug('App created, arrays loaded');
unset($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);

App::$user = new User();
App::$template = new Template();
App::$message = new Message();
App::$routing = new Routing (App::$server['REQUEST_URI']);

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

if(App::$server['SERVER_PROTOCOL']) {
    session_cache_limiter('nocache');
    session_name($SESSID);
    session_start();
    if( ! App::$user->authBySession($_SESSION) ) {
        App::$user->authByRememberme($COOKIE_NAME);
    }
    require_once __DIR__.'/lib_stats.php';
}


$part = App::$routing->getPartArray();
if (!$part['id']) {
    App::$message->get('default_tpl_not_found');
    exit();
}
$App->set('tpl_default', $part['tpl_name']);

if(!App::$user->checkAccess($part['user_flag'])) {
    if (App::$user->id) {
        $content = App::$message->get('error', [] ,'У вас нет соответствующих прав !');
        echo get_tpl_default([], null, $content);
    } else {
        $_SESSION['GO_TO_URI'] = App::$server['REQUEST_URI'];
        redirect(App::$SUBDIR . 'login/');
    }
    exit;
}

$server['PHP_SELF_DIR']=dirname(App::$server['PHP_SELF']).'/';

$content='';
$tags['INCLUDE_HEAD']='';
$tags['INCLUDE_CSS']='';
$tags['INCLUDE_JS']='';

$App->debug('common.php complete');
