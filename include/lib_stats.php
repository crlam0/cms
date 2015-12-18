<?php

/*
  create table visitor_log(
  id int(11) not null auto_increment,
  date datetime,
  day varchar(16),
  remote_addr varchar(16),
  remote_host varchar(32),
  user_agent varchar(64),
  script_name varchar(32),
  request_uri varchar(255),
  uid int(11),
  unique_visitor int(1) not null default '1',
  primary key(id),
  key (day),
  key (remote_addr),
  key (remote_host),
  key (user_agent),
  key (script_name),
  key (uid),
  key (unique_visitor)
  );

 */

$closed_urls=array('img','image','admin/','favicon');

$closed_url=0;
foreach($closed_urls as $url){
    if(strstr($_SERVER["REQUEST_URI"],$url)){
        $closed_url=1;
    }
}

if (!$closed_url) {
    /* 	if(!$_COOKIE[$COOKIE_NAME."_STATS"]){
      setcookie($COOKIE_NAME."_STATS", time(), time()+$settings[stats_cookie_hours]*3600);
      $unique=1;
      }else{
      $unique=0;
      }
     */
    $unique=0;
    $query="select id from visitor_log where remote_addr='" . $_SERVER["REMOTE_ADDR"] . "'";
    $result=my_query($query, $conn, true);
    if(!$result->num_rows)$unique=1;    
    $data['date']='now()';
    $data['day']="date_format(now(),'%Y-%m-%d')";
    $data['unique_visitor']=$unique;
    $uid = 0;
    if ($_SESSION["UID"])$data['uid'] = $_SESSION["UID"];
    $data['remote_addr']=$_SERVER['REMOTE_ADDR'];
    if (!$_SERVER["REMOTE_HOST"])$data['remote_host'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $data['script_name']=$_SERVER["SCRIPT_NAME"];
    $data['request_uri'] = $_SERVER['REQUEST_URI'];
    if (strlen($SUBDIR) > 1)$data['script_name'] = str_replace($SUBDIR, "/", $data['script_name']);    
    if (strlen($SUBDIR) > 1)$data['request_uri'] = str_replace($SUBDIR, "/", $data['request_uri']);
    $data['user_agent']=$_SERVER["HTTP_USER_AGENT"];

    $query = "insert into visitor_log" . db_insert_fields($data);
    my_query($query, $conn, 1);
}
?>