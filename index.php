<?php
$tags['Add_CSS'].=';blog_comments';
include 'include/common.php';

if($SUBDIR !== '/') {
    $request_uri = str_replace($SUBDIR, '', $server['REQUEST_URI']);
} else {
    $request_uri = substr($server['REQUEST_URI'], 1);
}    

if( ($request_uri==='' or $request_uri==='index.php') and file_exists('index.local.php')) {
    require 'index.local.php';
    exit;
}

$routes = require $INC_DIR . 'config/routes.global.php';

if(file_exists($INC_DIR . 'config/routes.local.php')) {
    $routes = array_merge($routes, require $INC_DIR . 'config/routes.local.php');
}

foreach($routes as $title => $route) {
    if (preg_match('/'.$route['pattern'].'/', $request_uri, $matches) === 1) {
        add_to_debug("Match route '{$title}'");
        foreach($matches as $key => $value){
            if($key==0){
                $file=dirname(__FILE__) . DIRECTORY_SEPARATOR . $route['file'];
            } else {
                $input[$route['params'][$key]]=htmlspecialchars($value);
            }
        }
        break;
    }
}

if(is_file($file)) {
    // error_reporting(0);    
    $server['PHP_SELF'] = $SUBDIR.$route['file'];
    $server['PHP_SELF_DIR'] = $SUBDIR.dirname($route['file']) . '/';

    require $file;
    exit;
} 

$tags['Header'] = 'Ошибка 404';
$tags['file_name'] = $server['REQUEST_URI'];
$content = my_msg_to_str('file_not_found', $tags, '');
header($server['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);


