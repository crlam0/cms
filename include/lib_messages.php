<?php
/*
Messages library.
*/

/* print message from DB table messages */
function my_msg_to_str($title,$tags=array(),$str=""){
	global $conn,$settings;
        if(strlen($title)){
            $sql="select * from messages where title='$title'";
            $msg=my_select_row($sql,1);		
        }
	if(strlen($str)) $msg[content]=$str;
	if(!strlen($msg[content])){
		return "";
	}
	if(is_array($tags))foreach ($tags as $key => $value) { $msg[content]=str_replace("[%".$key."%]",$value,$msg[content]); }
	if(!$msg[type])$msg[type]="info";
	if($msg){
		return "<div align=center><div class=msg_$msg[type] bgcolor=$bgcolor><font class=$msg[type]>$msg[content]</font></div></div>";
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
	if($settings[debug])print("<center><font class=debug>$str</font></center>");
}
function print_arr($arr){
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

/* inserts record to admin_log */
function admin_log($msg){
	global $conn,$_SESSION;
	$query="insert into admin_log(user_id,date,msg) values('".$_SESSION[UID]."',now(),'$msg')";
	my_query($query);
}

function send_mail ($msg_to,$subject,$msg) {
	global $settings;
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/plain; charset=windows-1251\r\n";
	$headers .= "From: $settings[email_from_addr]\r\n";
        $subject = iconv('UTF-8', 'windows-1251', $subject);
        $msg = iconv('UTF-8', 'windows-1251', $msg);
	mail($msg_to, $subject, $msg, $headers);
}

function my_send_mail ($tpl_title,$msg_to,$subject,$tags){
	$msg=get_tpl_by_title($tpl_title,$tags);
	send_mail($msg_to,$subject,$msg);	
}

?>