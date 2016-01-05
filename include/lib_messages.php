<?php
/*
Messages library.
*/

/* print message from DB table messages */
function my_msg_to_str($title,$tags=array(),$str=""){
	global $conn,$settings;
        if(strlen($title)){
            $sql="select * from messages where title='$title'";
            $message=my_select_row($sql,1);		
        }
	if(strlen($str)) $message[content]=$str;
	if(!strlen($message[content])){
		return "";
	}
	if(is_array($tags))foreach ($tags as $key => $value) { $message[content]=str_replace("[%".$key."%]",$value,$message[content]); }
	if(!$message[type])$message[type]="info";
	if($message){
		return "<div align=center><div class=msg_{$message['type']} bgcolor=$bgcolor><font class={$message['type']}>{$message['content']}</font></div></div>";
	}
}
function my_msg($title,$tags=array(),$str=""){
	global $conn,$settings;
	echo my_msg_to_str($title,$tags,$str);	
}
function print_ok($str){
	my_msg("info","",$str);
}
function print_err($str){
	my_msg("error","",$str);
}
function print_debug($str){
	global $settings;
	if($settings['debug'])print("<center><font class=debug>{$str}</font></center>");
}
function print_arr($arr){
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

/* inserts record to admin_log */
function admin_log($message){
	global $conn,$_SESSION;
	$query="insert into admin_log(user_id,date,msg) values('".$_SESSION[UID]."',now(),'{$message}')";
	my_query($query);
}

function send_mail ($message_to,$subject,$message) {
	global $settings;
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/plain; charset=windows-1251\r\n";
	$headers .= "From: {$settings['email_from_addr']}\r\n";
        $subject = iconv('UTF-8', 'windows-1251', $subject);
        $message = iconv('UTF-8', 'windows-1251', $message);
	mail($message_to, $subject, $message, $headers);
}

function my_send_mail ($tpl_title,$message_to,$subject,$tags){
	$message=get_tpl_by_title($tpl_title,$tags);
	send_mail($message_to,$subject,$message);	
}

?>