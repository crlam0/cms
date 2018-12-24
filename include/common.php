<?php

$DEBUG['start_time'] = microtime(true);

function add_to_debug ($message) {
    global $DEBUG;
    $time = microtime(true) - $DEBUG['start_time'];
    $time = sprintf('%.4F', $time);
    $DEBUG['log'][] = $time . "\t " . $message;
}

require 'config/config.local.php';
require 'config/misc.php';

if(file_exists($INC_DIR.'config/misc.local.php')) {
    require_once $INC_DIR.'config/misc.local.php';
}    

$content='';
$_SESSION['UID']=0;
$_SESSION['FLAGS']='';

if($_SERVER['SERVER_PROTOCOL']) {
    session_cache_limiter('nocache');
    session_name($SESSID);
    session_start();    
} else {
    $DIR=dirname(dirname(__FILE__)) . '/';
    $INC_DIR=$DIR.'include/';
}
add_to_debug('Local configs loaded, session started');

if(file_exists($DIR.'vendor/autoload.php')) {
    require_once $DIR.'vendor/autoload.php';
}    
add_to_debug('Autoload classes complete');


require $INC_DIR.'lib_sql.php';

add_to_debug('SQL base connected');

use Classes\MyArray;
$input = new MyArray;
$server = new MyArray;
if(is_array($_GET))foreach ($_GET as $key => $value){
    $input[$key]=db_test_param($value,$key);
}
if(is_array($_POST))foreach ($_POST as $key => $value){
    $input[$key]=db_test_param($value,$key);
}
if(is_array($_SERVER))foreach ($_SERVER as $key => $value){
    $server[$key]=$value;
}

use Classes\MyGlobal;
MyGlobal::set('input', $input );
MyGlobal::set('server', $server );
MyGlobal::set('DIR', $DIR );
MyGlobal::set('SUBDIR', $SUBDIR );
add_to_debug('Global arrays loaded');

use Classes\Routing;
$Routing = new Routing ($server['REQUEST_URI']);
MyGlobal::set('Routing', $Routing );
if ($Routing->hasGETParams()) {
    $Routing->proceedGETParams();
}
add_to_debug('Routing added');

// Load settings into $settings[]
$settings = new MyArray;
if(file_exists($INC_DIR.'config/settings.local.php')) {
    $settings_local=(require $INC_DIR . 'config/settings.local.php');
    if(is_array($settings_local)) {
        foreach ($settings_local as $key => $value){
            $settings[$key]=$value;
        }
    }    
}    
$query='SELECT * FROM settings';
$result=$DB->query($query,true);
while ($row = $result->fetch_array()) {
    $settings[$row['title']] = $row['value'];
}
MyGlobal::set('settings', $settings );
add_to_debug('Settings loaded');

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
if($settings['debug']) {
    $whoops = new Run();
    $whoops->writeToOutput(true);
    $whoops->allowQuit(true);
    $PrettyPageHandler = new PrettyPageHandler();
    $PrettyPageHandler->addDataTable('DEBUG Array',  $DEBUG['log']);
    $whoops->pushHandler($PrettyPageHandler);
    $whoops->register();
    add_to_debug('Added exception handler');
}

require $INC_DIR.'lib_messages.php';
require $INC_DIR.'lib_templates.php';
require $INC_DIR.'lib_functions.php';
require $INC_DIR.'lib_url.php';
add_to_debug('Library loaded');

require_once $INC_DIR.'lib_stats.php';
add_to_debug('Stats added');

$part = $Routing->getPartArray();
if (!$part['id']) {
    my_msg('default_tpl_not_found');
    exit();
}
MyGlobal::set('tpl_default', $part['title']);

add_to_debug('Part data loaded');

if ((strlen($part['user_flag'])) && (!have_flag($part['user_flag'])) && (!have_flag('global')) ) {
    if (isset($_SESSION['UID'])) {
        $content ='<h1 align=center>У вас нет соответствующих прав !</h1>';
        echo get_tpl_default([], null, $content);
    } else {
        $_SESSION['GO_TO_URI'] = $server['REQUEST_URI'];
        redirect($SUBDIR . 'login/');
    }
    exit;
}

add_to_debug('User flag checked');

$server['PHP_SELF_DIR']=dirname($server['PHP_SELF']).'/';

$tags['INCLUDE_HEAD']='';
$tags['INCLUDE_CSS']='';
$tags['INCLUDE_JS']='';

add_to_debug('common.php complete');
