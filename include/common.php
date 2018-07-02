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
$_SESSION['UNAME']='';
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

if(isset($input)) {
    MyGlobal::set('input', $input );
}    
MyGlobal::set('server', $server );
MyGlobal::set('DIR', $DIR );
MyGlobal::set('SUBDIR', $SUBDIR );

add_to_debug('Global arrays loaded');

require $INC_DIR.'lib_messages.php';
require $INC_DIR.'lib_templates.php';
require $INC_DIR.'lib_functions.php';

add_to_debug('Library loaded');

// Load settings into $settings[]
$settings = new MyArray;
$query='SELECT * FROM settings';
$result=$DB->query($query,true);
while ($row = $result->fetch_array()) {
    $settings[$row['title']] = $row['value'];
}

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
    add_to_debug('Add exception handler');
}


require_once $INC_DIR.'lib_stats.php';

add_to_debug('Stats added');

$query = "SELECT * FROM parts WHERE uri='" . $server["REQUEST_URI"] . "'";
$part = my_select_row($query, 1);
if (!$part['id']) {
    $query = "SELECT * FROM parts WHERE '" . $server["REQUEST_URI"] . "' LIKE concat('%',uri,'%') AND title<>'default'";
    $part = my_select_row($query, 1);
}
if (!$part['id']) {
    $query = "SELECT * FROM parts WHERE title='default'";
    $part = my_select_row($query, 1);
}
if (!$part['id']) {
    my_msg('default_tpl_not_found');
    exit();
}

add_to_debug('Part data loaded');

if (array_key_exists('FLAGS',$_SESSION) && (strlen($part['user_flag'])) && (!strstr($_SESSION['FLAGS'], $part['user_flag'])) && (!strstr($_SESSION['FLAGS'], 'global'))) {
    if ($_SESSION['UID']) {
        $content ='<h1 align=center>У вас нет соответствующих прав !</h1>';
        echo get_tpl_by_title($part['tpl_name'], [], null, $content);
    } else {
        $_SESSION['GO_TO_URI'] = $server['REQUEST_URI'];
        redirect($SUBDIR . 'login/');
    }
    exit;
}

add_to_debug('User flag checked');

$server['PHP_SELF_DIR']=dirname($server['PHP_SELF']).'/';

if(array_key_exists('Add_CSS', $tags)) {
    $settings['css_list'] .= $tags['Add_CSS'];
}

$css_array=explode(';',$settings['css_list']);
if(!array_key_exists('INCLUDE_CSS', $tags)) {
    $tags['INCLUDE_CSS']='';
}
foreach ($css_array as $css){
    $css='css/'.$css.'.css';
    if(file_exists($DIR.$css)){
        $tags['INCLUDE_CSS'].='<link href="'.$SUBDIR.$css.'" type="text/css" rel=stylesheet />'."\n";
    } else {
        add_to_debug('CSS file missing: ' . $DIR.$css);
    }
}
unset($css_array,$css);
$tags['INCLUDE_HEAD']='';
$tags['INCLUDE_JS']='';

add_to_debug('common.php complete');
