<?php

include 'include/common.php';

use classes\App;

if( isset($REDIRECT_TO_HTTPS) && App::$server['REQUEST_SCHEME'] === 'http' ){
    $url = 'https://' . App::$server['HTTP_HOST'] . '' . App::$server['REQUEST_URI'];
    redirect($url);
}

$App->run($tags);

$tags['Header'] = 'Ошибка 404';
$tags['file_name'] = App::$server['REQUEST_URI'];
$content = App::$message->get('file_not_found', $tags);
App::sendResult($content, $tags, 404);

