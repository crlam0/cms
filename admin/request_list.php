<?php

$tags['Header'] = 'Список заказов';
include_once "../include/common.php";

use classes\App;

if ($input['active']) {
    $query = "update request set active=? where id=?";
    App::$db->query($query, ['active' => $input['active'], 'id' => $input['id']]);
    $view = 1;
}

if ($input['del']) {
    App::$db->deleteFromTable('request', ['id' => $input['id']]);
}

function file_info($tmp, $row): string
{
    global $DIR, $SUBDIR, $settings;
    if (isset($row['file_name']) && is_file($DIR . $settings['files_upload_path'] . $row['file_name'])) {
        return "<br />Прикреплен файл: <a href=\"{$SUBDIR}{$settings['files_upload_path']}{$row['file_name']}\" target=\"_blank\">{$row['file_name']}</a>";
    } else {
        return '';
    }
}

$query = "SELECT * from request order by id desc";
$result = App::$db->query($query);

$content .= App::$template->parse('request_list', $tags, $result);
echo  App::$template->parse($part['tpl_name'], $tags, null, $content);
