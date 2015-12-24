<?php

/*
CREATE TABLE `visitor_log` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `date` DATETIME NULL DEFAULT NULL,
    `day` VARCHAR(16) NULL DEFAULT NULL,
    `remote_addr` VARCHAR(16) NULL DEFAULT NULL,
    `remote_host` VARCHAR(255) NULL DEFAULT NULL,
    `user_agent` VARCHAR(64) NULL DEFAULT NULL,
    `script_name` VARCHAR(64) NULL DEFAULT NULL,
    `request_uri` VARCHAR(255) NULL DEFAULT NULL,
    `uid` INT(11) NULL DEFAULT NULL,
    `unique_visitor` INT(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`),
    INDEX `day` (`day`),
    INDEX `remote_addr` (`remote_addr`),
    INDEX `remote_host` (`remote_host`),
    INDEX `user_agent` (`user_agent`),
    INDEX `script_name` (`script_name`),
    INDEX `uid` (`uid`),
    INDEX `unique_visitor` (`unique_visitor`)
)

 */

$closed_urls=array('img','image','admin/','favicon');

$closed_url=0;
foreach($closed_urls as $url){
    if(strstr($server["REQUEST_URI"],$url)){
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
    $query="select id from visitor_log where remote_addr='" . $server["REMOTE_ADDR"] . "'";
    $result=my_query($query, $conn, true);
    if(!$result->num_rows)$unique=1;    
    $data['date']='now()';
    $data['day']="date_format(now(),'%Y-%m-%d')";
    $data['unique_visitor']=$unique;
    $uid = 0;
    if ($_SESSION["UID"])$data['uid'] = $_SESSION["UID"];
    $data['remote_addr']=$server['REMOTE_ADDR'];
    if (!$server["REMOTE_HOST"])$data['remote_host'] = gethostbyaddr($server['REMOTE_ADDR']);
    $data['script_name']=$server["SCRIPT_NAME"];
    $data['request_uri'] = $server['REQUEST_URI'];
    if (strlen($SUBDIR) > 1)$data['script_name'] = str_replace($SUBDIR, "/", $data['script_name']);    
    if (strlen($SUBDIR) > 1)$data['request_uri'] = str_replace($SUBDIR, "/", $data['request_uri']);
    $data['user_agent']=$server["HTTP_USER_AGENT"];

    $query = "insert into visitor_log" . db_insert_fields($data);
    my_query($query, $conn, 1);
}
?>