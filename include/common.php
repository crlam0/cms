<?php

if (file_exists(__DIR__.'/../local/config.php')) {
    require __DIR__.'/../local/config.php';
} else {
    die('Main config not found');
}
require __DIR__.'/config/misc.php';

if (file_exists(__DIR__.'/../local/misc.local.php')) {
    require_once __DIR__.'/../local/misc.local.php';
}

if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require_once __DIR__.'/../vendor/autoload.php';
} else {
    die('Cant find autoloader');
}

use classes\App;
use classes\DB;
use classes\Routing;
use classes\User;
use classes\Template;
use classes\Message;
use classes\Session;
use classes\FileCache;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

App::$logger = new Logger('main');
App::$logger->pushHandler(new StreamHandler($DIR . 'var/log/error.log', Logger::ERROR));

$App = new App($DIR, $SUBDIR);
$App->setDB(new DB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME));
$App->loadSettings(__DIR__ . '/../local/settings.php');
$App->loadInputData($_GET, $_POST, $_SERVER);
$App->addGlobals();
App::$debug = App::$settings['debug'];
App::$db->debug = App::$settings['debug'];

if (App::$debug) {
    App::$logger->pushHandler(new StreamHandler($DIR . 'var/log/debug.log', Logger::DEBUG));
    if (class_exists('Whoops\Run')) {
        $whoops = new Run();
        $whoops->writeToOutput(true);
        $whoops->allowQuit(true);
        $PrettyPageHandler = new PrettyPageHandler();
        $PrettyPageHandler->addDataTable('DEBUG Array', App::$DEBUG_ARRAY);
        $whoops->pushHandler($PrettyPageHandler);
        $whoops->register();
        $App->debug('Added exception handler');
    }
}

App::debug('App created, arrays loaded');
unset($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);

App::$user = new User(null, App::$settings['default_flags']);
App::$routing = new Routing(App::$server['REQUEST_URI']);
App::$message = new Message();
App::$template = new Template();
App::$cache = new FileCache('var/cache/misc/');
App::$session = new Session();

require_once __DIR__.'/lib_sql.php';
require_once __DIR__.'/lib_messages.php';
require_once __DIR__.'/lib_templates.php';
require_once __DIR__.'/lib_functions.php';

$App->debug('Library loaded');

if (App::$server['SERVER_PROTOCOL']) {
    App::$session->setName($SESSID);
    App::$session->open();
    if (! App::$user->authBySession(App::$session)) {
        App::$user->authByRememberme($COOKIE_NAME);
    }
    require_once __DIR__.'/lib_stats.php';
    $content='';
    $tags['INCLUDE_HEAD']='';
    $tags['INCLUDE_CSS']='';
    $tags['INCLUDE_JS']='';
    $server['PHP_SELF_DIR']=dirname(App::$server['PHP_SELF']).'/';
}

$part = App::$routing->getPartArray();
if (!$part['id']) {
    App::$message->get('default_tpl_not_found');
    exit();
}
$App->set('tpl_default', $part['tpl_name']);

if (!App::$user->checkAccess($part['user_flag'])) {
    if (App::$user->id) {
        App::sendResult(App::$message->getError('У вас нет соответствующих прав !'), $tags, 403);
    } else {
        App::$session['GO_TO_URI'] = App::$server['REQUEST_URI'];
        redirect(App::$SUBDIR . 'login/');
    }
    exit;
}


if (isset(App::$settings['modules'])) {
    foreach (App::$settings['modules'] as $name => $data) {
        if (isset($data['bootstrap'])) {
            if (class_exists($data['bootstrap'])) {
                $bootstrap = new $data['bootstrap'];
                App::debug('Run boostrap for module "' . $name .'"');
                $bootstrap->bootstrap();
            } else {
                App::debug('Boostrap class for module "' . $name .'" decalred but not exists: "' . $data['bootstrap'] . '"');
            }
        }
    }
}

App::$routing->matchRoutes();

$App->debug('common.php complete');
