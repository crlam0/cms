<?php

use classes\App;

$deny_urls=array('img','image','admin/','favicon');
$deny_remote_hosts=array('bot','spider','yandex','google','mail.ru','crawl');
$deny_user_agents=array('bot','spider','YandexMetrika','Yahoo');

$deny = false;
foreach($deny_urls as $url){
    if(strstr($server['REQUEST_URI'],$url)){
        $deny = true ;
    }
}
foreach($deny_remote_hosts as $host){
    if($server->keyExists('REMOTE_HOST') && stristr($server['REMOTE_HOST'],$host)){
        $deny = true;
    }
}
foreach($deny_user_agents as $agent){
    if(stristr($server['HTTP_USER_AGENT'],$agent)){
        $deny = true;
    }
}

if (!$deny) {
    $unique=0;
    $query="SELECT id FROM visitor_log WHERE remote_addr='" . $server['REMOTE_ADDR'] . "'";
    $result=App::$db->query($query);
    if(!$result->num_rows) {
        $unique=1;
    }
    $data['date']='now()';
    $data['day']="date_format(now(),'%Y-%m-%d')";
    $data['unique_visitor']=$unique;
    $uid = 0;
    $data['uid'] = App::$user->id;
    $data['remote_addr']=$server['REMOTE_ADDR'];
    $data['remote_host']=($server->keyExists('REMOTE_HOST') && $server['REMOTE_HOST'] ? $server['REMOTE_HOST'] : gethostbyaddr($server['REMOTE_ADDR']) );
    $data['script_name']=$server['SCRIPT_NAME'];
    $data['request_uri'] = $server['REQUEST_URI'];
    if (strlen($SUBDIR) > 1){
        $data['script_name'] = str_replace($SUBDIR, "/", $data['script_name']);
    }
    if (strlen($SUBDIR) > 1){
        $data['request_uri'] = str_replace($SUBDIR, "/", $data['request_uri']);
    }
    $data['user_agent']=$server['HTTP_USER_AGENT'];

    App::$db->insertTable('visitor_log', $data);
    unset($data);
}
