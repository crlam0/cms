<?php
$tags[Header]="Список заказов";
include_once "../include/common.php";

if($_GET["active"]){
        $query="update request set active='".$_GET["active"]."' where id=".$_GET["id"];
        $result=my_query($query);
        $view=1;
}

if ($input["del"]) {
    $query = "delete from request where id='$input[id]'";
    my_query($query, null, true);
}

function file_info($tmp, $row) {
    global $DIR, $SUBDIR, $settings;
    if (is_file($DIR . $settings['files_upload_path'] . $row['file_name'])) {
        return "<br />Прикреплен файл: <a href=\"{$SUBDIR}{$settings['files_upload_path']}{$row['file_name']}\" target=\"_blank\">{$row['file_name']}</a>";
    } else {
        return '';
    }
}


$query="SELECT * from request order by id desc";
$result=my_query($query);

$content.=get_tpl_by_name("request_list",$tags,$result);
echo get_tpl_by_name($part[tpl_name],$tags,"",$content);
?>
