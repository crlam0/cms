<?php

use classes\App;

$deny_urls=array('img','image','admin/','favicon');
$deny_remote_hosts=array('bot','spider','yandex','google','mail.ru','crawl');
$deny_user_agents=array('bot','spider','YandexMetrika','Yahoo');

$deny=0;
foreach($deny_urls as $url){
    if(strstr($server['REQUEST_URI'],$url)){
        $deny=1;
    }
}
foreach($deny_remote_hosts as $host){
    if(array_key_exists('REMOTE_HOST', $server) && stristr($server['REMOTE_HOST'],$host)){
        $deny=1;
    }
}
foreach($deny_user_agents as $agent){
    if(stristr($server['HTTP_USER_AGENT'],$agent)){
        $deny=1;
    }
}

if (!$deny) {
    /* 	if(!$_COOKIE[$COOKIE_NAME."_STATS"]){
      setcookie($COOKIE_NAME."_STATS", time(), time()+$settings[stats_cookie_hours]*3600);
      $unique=1;
      }else{
      $unique=0;
      }
     */
    $unique=0;
    $query="SELECT id FROM visitor_log WHERE remote_addr='" . $server['REMOTE_ADDR'] . "'";
    $result=App::$db->query($query);
    if(!$result->num_rows)$unique=1;    
    $data['date']='now()';
    $data['day']="date_format(now(),'%Y-%m-%d')";
    $data['unique_visitor']=$unique;
    $uid = 0;
    $data['uid'] = App::$user->id;
    $data['remote_addr']=$server['REMOTE_ADDR'];
    $data['remote_host']=(array_key_exists('REMOTE_HOST', $server) && $server['REMOTE_HOST'] ? $server['REMOTE_HOST'] : gethostbyaddr($server['REMOTE_ADDR']) );
    $data['script_name']=$server['SCRIPT_NAME'];
    $data['request_uri'] = $server['REQUEST_URI'];
    if (strlen($SUBDIR) > 1){
        $data['script_name'] = str_replace($SUBDIR, "/", $data['script_name']);
    }
    if (strlen($SUBDIR) > 1){
        $data['request_uri'] = str_replace($SUBDIR, "/", $data['request_uri']);
    }
    $data['user_agent']=$server['HTTP_USER_AGENT'];

    $query = "insert into visitor_log" . db_insert_fields($data);
    App::$db->query($query, true);
    unset($data);
}
