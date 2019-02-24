<?php

include 'include/common.php';

if( isset($REDIRECT_TO_HTTPS) && $server['REQUEST_SCHEME'] === 'http' ){
    $url = 'https://' . $server['HTTP_HOST'] . '' . $server['REQUEST_URI'];
    redirect($url);
}

use Classes\MyGlobal;

$Routing = MyGlobal::get('Routing');

if( $Routing->isIndexPage() and file_exists('index.local.php')) {
    $tags['isIndexPage'] = true;
    require 'index.local.php';
    exit;
}

$file = $Routing->getFileName();

if($file && is_file($DIR . $file)) {
    // error_reporting(0);    
    $server['PHP_SELF'] = $SUBDIR.$file;
    $server['PHP_SELF_DIR'] = $SUBDIR.dirname($file) . '/';

    require $DIR . $file;
    exit;
} 

$tags['Header'] = 'Ошибка 404';
$tags['file_name'] = $server['REQUEST_URI'];
$content = my_msg_to_str('file_not_found',$tags);
header($server['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
echo get_tpl_default($tags, null, $content);


