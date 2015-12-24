<?php
$tags[Header]="Новости";
include "../include/common.php";

$query="select * from news ".($input["show"]?" where id='".$input["show"]."' ":"")."order by date desc".($input["show_all"]?"":" limit ".$settings[news_count]);
$result=my_query($query,$conn,1);

if($input["show"]){
	$tags[nav_str].="<a href=".$server["PHP_SELF"]." class=nav_next>$tags[Header]</a>";
	$row=$result->fetch_array();
	$tags[nav_str].="<span class=nav_next>$row[title]</span>";
        $tags[Header].=" - ".$row[title];
	$result->data_seek(0);	
}else{
	$tags[nav_str].="<span class=nav_next>$tags[Header]</span>";
}

function get_news_full_content($tmp,$row){
        return replace_base_href($row[content]);
}

if(!$result->num_rows){
	$content=my_msg_to_str("$part_empty");
}else{
	$content=get_tpl_by_title("news_table",$tags,$result);
}
echo get_tpl_by_title("$part[tpl_name]",$tags,"",$content);

?>