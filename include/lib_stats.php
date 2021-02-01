<?php

use classes\App;

$deny_urls=['img','image','admin/','favicon'];
$deny_remote_hosts=['bot','spider','yandex','google','mail.ru','crawl'];
$deny_user_agents=['bot','spider','YandexMetrika','Yahoo'];

$deny = false;
foreach ($deny_urls as $url) {
    if (strstr(App::$server['REQUEST_URI'], $url)) {
        $deny = true ;
    }
}
foreach ($deny_remote_hosts as $host) {
    if (App::$server->keyExists('REMOTE_HOST') && stristr(App::$server['REMOTE_HOST'], $host)) {
        $deny = true;
    }
}
foreach ($deny_user_agents as $agent) {
    if (stristr(App::$server['HTTP_USER_AGENT'], $agent)) {
        $deny = true;
    }
}

if (!$deny) {
    $unique=0;
    $query="SELECT id FROM visitor_log WHERE remote_addr=?";
    $result=App::$db->query($query, ['remote_addr' => App::$server['REMOTE_ADDR']]);
    if (!$result->num_rows) {
        $unique = 1;
    }
    $data['date'] = 'now()';
    $data['day'] = "date_format(now(),'%Y-%m-%d')";
    $data['unique_visitor'] = $unique;
    $data['uid'] = App::$user->id;
    $data['remote_addr'] = App::$server['REMOTE_ADDR'];
    $data['remote_host'] = (App::$server->keyExists('REMOTE_HOST') && App::$server['REMOTE_HOST'] ? App::$server['REMOTE_HOST'] : gethostbyaddr(App::$server['REMOTE_ADDR']) );
    $data['script_name'] = App::$server['SCRIPT_NAME'];
    $data['request_uri'] = App::$server['REQUEST_URI'];
    if (strlen($SUBDIR) > 1) {
        $data['script_name'] = str_replace($SUBDIR, "/", $data['script_name']);
    }
    if (strlen($SUBDIR) > 1) {
        $data['request_uri'] = str_replace($SUBDIR, "/", $data['request_uri']);
    }
    $data['user_agent'] = App::$server['HTTP_USER_AGENT'];

    App::$db->insertTable('visitor_log', $data);
    unset($data);
}
