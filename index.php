<?php
include 'include/common.php';

if($SUBDIR !== '/') {
    $request_uri = str_replace($SUBDIR, '', $server['REQUEST_URI']);
} else {
    $request_uri = substr($server['REQUEST_URI'], 1);
}    

if(strstr($request_uri,'?')) {
    $get_param=substr($request_uri,strpos($request_uri,'?')+1);
    $request_uri = substr($request_uri,0,strpos($request_uri,'?'));
    if(strpos($get_param,'&')){
        $get_array = explode('&',$get_param);
    } else {
        $get_array[] = $get_param;
    }
    foreach ($get_array as $param) {
        if(strpos($param,'=')) {
            $param_array = explode('=',$param);
            $input[$param_array[0]] = $param_array[1];
        } else {
            $input[$param] = '';
        }
    }
    unset($get_param, $get_array, $param, $param_array);
}

if( ($request_uri==='' or $request_uri==='/') and file_exists('index.local.php')) {
    require 'index.local.php';
    exit;
}

if(strstr($request_uri,'modules/')) {
    $request_uri = str_replace('modules/', '', $request_uri);
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
            } elseif(array_key_exists('params', $route)) {
                $input[$route['params'][$key]]=htmlspecialchars($value);
            }
        }
        break;
    }
}

if(isset($file) && is_file($file)) {
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


