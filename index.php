<?php

include 'include/common.php';

use classes\App;

if( isset($REDIRECT_TO_HTTPS) && App::$server['REQUEST_SCHEME'] === 'http' ){
    $url = 'https://' . App::$server['HTTP_HOST'] . '' . App::$server['REQUEST_URI'];
    redirect($url);
}

/* I know, it's bad code. But its simply way for landings */
if( App::$routing->isIndexPage() && file_exists('index.local.php')) {
    $tags['isIndexPage'] = true;
    require 'index.local.php';
    exit;
}

$App->run($tags);

$tags['Header'] = 'Ошибка 404';
$tags['file_name'] = App::$server['REQUEST_URI'];
$content = App::$message->get('file_not_found',$tags);
header(App::$server['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
echo App::$template->parse(App::get('tpl_default'), $tags, null, $content);

