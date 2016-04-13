<?php
include "../inc/common.php";
$tags[Header]="Форма для заявки";

$content="
</center><br>
<table width=100% border=0 cellspacing=1 cellpadding=1 class=admin align=center>
<tr class=header align=center>
<td width=20%>Дата</td>
<td width=80%>Сообщение</td>
</tr>
";


$query="select * from requests order by id desc";
$result=my_query($query,$conn);
while ($row = mysql_fetch_array($result)){
	$content.="
        <tr class=content>
        <td align=center>$row[date]</td>
        <td align=left>".nl2br($row[msg])."</td>
        </tr>
        ";
}
$content ."</table>";

echo get_tpl_by_title("$part[tpl_name]",$tags,"",$content);

?>