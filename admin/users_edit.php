<?php
$tags[Header]="Пользователи сервера";
include "../include/common.php";

ob_start();

$stris=count($input);
if(!$stris)$view=1;

if ($_GET["del_user"]){
	$query="delete from users where id=".$_GET["uid"];
	my_query($query);
	$content.=my_msg_to_str("", "", "Пользователь успешно удален !");
	$view=1;
}

if ($_POST["edit_user"]){
	$flags=@implode(";", $_POST["flags"]);
        if(strlen($_POST["passwd"]))$passwd_inc="passwd='".md5($_POST["passwd"])."',";
	$query="update users set login='".$_POST["login"]."',".$passwd_inc."email='".$_POST["email"]."',fullname='".$_POST["fullname"]."',flags='$flags' where id=".$_POST["uid"]."";
	my_query($query);
	$content.=my_msg_to_str("", "", "Редактирование успешно завершено !");
	$view=1;
}

if ($_POST["add_user"]){
	$query="select id from users where login='".$_POST["login"]."'";
	$result=my_query($query);
	if($result->num_rows){
		$content.=my_msg_to_str("error","","Такой пользователь уже существует ! (".$_POST["login"].")");
	}else{
		$flags=@implode(";", $_POST["flags"]);
                if(strlen($_POST["passwd"]))$_POST["passwd"]=md5($_POST["passwd"]);
		$query="INSERT INTO users(login,passwd,fullname,email,flags,regdate) values('".$_POST["login"]."','".$_POST["passwd"]."','".$_POST["fullname"]."','".$_POST["email"]."','$flags',now())";
		my_query($query);
		$query="select last_insert_id()";
		$result=my_query($query);
		list($_POST["uid"])=$result->fetch_array();
		$content.=my_msg_to_str("", "", "Пользователь успешно добавлен !");
		$view=1;
	}
}

if ( ($_GET["edit"]) || ($_GET["add"])){
	if ($_GET["edit"]){
		$query="SELECT id,login,passwd,email,fullname,regdate,flags from users where id='".$_GET["uid"]."'";
		$result=my_query($query);
		$row = $result->fetch_array();
		$flags=@explode(";",$row[flags]);
	}else{
		$flags=@explode(";","stat;passwd;");
	}
	echo "
        <form action=".$server["PHP_SELF"]." method=post>
        <input type=hidden name=uid value=$row[id]>
        <input type=hidden name=".(($_GET["edit"])||($_POST["add_ok"])?"edit_user":"add_user")." value=1>
        <table width=500 border=0 cellspacing=1 cellpadding=1 class=admin align=center>
        <tr class=header align=left><td>Имя:</td><td><input type=edit maxlength=16 name=login value=\"$row[login]\"></td></tr>
        <tr class=content align=left><td>Новый пароль:</td><td><input type=password maxlength=16 name=passwd value=\"\"></td></tr>
        <tr class=content align=left><td>Полное имя:</td><td><input type=edit maxlength=254 name=fullname value=\"$row[fullname]\"></td></tr>
        <tr class=content align=left><td>E-Mail:</td><td><input type=edit maxlength=32 name=email value=\"$row[email]\"></td></tr>
        <tr class=content align=left><td>Флаги:</td><td>";
		$query="select * from users_flags order by title asc";
		$result_flags=my_query($query,$conn,1);
		while($row_flags=$result_flags->fetch_array()){
			echo "<input type=checkbox name=flags[] ".(in_array($row_flags[value], $flags) ? "checked" : "")." value=\"$row_flags[value]\">$row_flags[title]<br>";
		}
        echo "</td></tr>
        <tr class=content align=left><td>Дата регистрации:</td><td>$row[regdate]</td></tr>
        <tr class=header align=left><td align=center colspan=2>
        <input type=submit value=\"  ".($_GET["edit"]?"Сохранить":"Добавить")."  \">
        </td></tr>
        </table>
        </form>
        ";

}

if($view){

	echo "<table width=500 border=0 cellspacing=1 cellpadding=1 class=admin align=center>
        <tr class=header align=center>
        <td>Имя</td>
        <td>Полное имя</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
        ";

	$query="select * from users order by login";
	$result=my_query($query);
	while ($row = $result->fetch_array()){
		echo "
                <tr class=content align=left>
                <td><b>$row[login]</b></td>
                <td>$row[fullname]</td>
                <td width=16><a href=".$server["PHP_SELF"]."?edit=1&uid=$row[id]><img src=\"../images/open.gif\" alt=\"Редактировать\" border=0></a></td>
                <td width=16><a href=".$server["PHP_SELF"]."?del_user=1&uid=$row[id]><img src=\"../images/del.gif\" alt=\"Удалить\" border=0 onClick=\"return test()\"></a></td>
                </tr>
                ";
	}
	echo "</table><br>
        <center>
        <form action=".$server["PHP_SELF"]." method=get>
        <input type=hidden name=add value=1>
        <input type=submit value=\"Добавить\">
        </form>
        </center>
        ";
}

$content=ob_get_contents();
ob_end_clean();
echo get_tpl_by_title($part[tpl_name],$tags,"",$content);
?>