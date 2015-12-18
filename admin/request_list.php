<?php
$tags[Header]="Список заказов";
include_once "../include/common.php";

if($_GET["active"]){
        $query="update request set active='".$_GET["active"]."' where id=".$_GET["id"];
        $result=my_query($query,$conn);
        $view=1;
}

if ($input["del"]) {
    $query = "delete from request where id='$input[id]'";
    my_query($query, $conn, 1);
}

$query="SELECT * from request order by id desc";
$result=my_query($query,$conn);

$content.=get_tpl_by_title("request_list",$tags,$result);
echo get_tpl_by_title($part[tpl_name],$tags,"",$content);
?>
