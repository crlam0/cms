<?php

include 'include/common.php';

use Classes\App;

if( isset($REDIRECT_TO_HTTPS) && App::$server['REQUEST_SCHEME'] === 'http' ){
    $url = 'https://' . App::$server['HTTP_HOST'] . '' . App::$server['REQUEST_URI'];
    redirect($url);
}

if( App::$routing->isIndexPage() && file_exists('index.local.php')) {
    $tags['isIndexPage'] = true;
    require 'index.local.php';
    exit;
}

if($controller_name = App::$routing->controller) {
//    try {
        $action = App::$routing->action;
        App::debug('Create controller "' . $controller_name . '" and run action "' . $action . '"');
        $controller = new $controller_name;
        $content = $controller->run($action, App::$routing->params);
        $tags['Header'] = $controller->title;
        $tags['nav_array'] = array_merge($tags['nav_array'],$controller->breadcrumbs);
        $tags = array_merge($tags,$controller->tags);
        echo App::$template->parse(App::get('tpl_default'), $tags, null, $content);
        exit;
//    } catch (Exception $e) {
//        App::debug('Exception: ' . $e->getMessage());
//    }
}

$file=App::$routing->file;
if($file && is_file($DIR . $file)) {
    $server['PHP_SELF'] = $SUBDIR.$file;
    $server['PHP_SELF_DIR'] = $SUBDIR.dirname($file) . '/';
    require $DIR . $file;
    exit;
} 

$tags['Header'] = 'Ошибка 404';
$tags['file_name'] = App::$server['REQUEST_URI'];
$content = my_msg_to_str('file_not_found',$tags);
header(App::$server['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
echo get_tpl_default($tags, null, $content);


