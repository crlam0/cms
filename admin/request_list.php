<?php
$tags['Header']='Список заказов';
include_once "../include/common.php";

if($input['active']){
        $query="update request set active='".$input['active']."' where id='{$input['id']}'";
        $result=my_query($query);
        $view=1;
}

if ($input['del']) {
    $query = "delete from request where id='{$input['id']}'";
    my_query($query, null, true);
}

function file_info($tmp, $row): string {
    global $DIR, $SUBDIR, $settings;
    if (isset($row['file_name']) && is_file($DIR . $settings['files_upload_path'] . $row['file_name'])) {
        return "<br />Прикреплен файл: <a href=\"{$SUBDIR}{$settings['files_upload_path']}{$row['file_name']}\" target=\"_blank\">{$row['file_name']}</a>";
    } else {
        return '';
    }
}


$query="SELECT * from request order by id desc";
$result=my_query($query);

$content.=get_tpl_by_name('request_list',$tags,$result);
echo get_tpl_default($tags, null, $content);

