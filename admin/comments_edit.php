<?php

$tags['Header'] = "Комментарии";
include "../include/common.php";

$TABLE = "comments";

if ($input["active"]) {
    $query = "update $TABLE set active='" . $input["active"] . "' where id=" . $input["id"];
    if (my_query($query, true)) {
        print $input["active"];
    } else {
        print mysql_error();
    }
    exit;
}

if ($input["del_comment"]) {
    $query = "delete from $TABLE where id={$input["id"]}";
    $result = my_query($query, true);
    $list = 1;
    $content.=my_msg_to_str("", "", "Комментарий успешно удален.");
}

if ($input["edited_comment"]) {
    $query = "update $TABLE set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query, true);
    $content.=my_msg_to_str("", "", "Комментарий успешно изменен.");
}

if (($input["edit_comment"]) || ($input["add_comment"])) {
    if ($input["edit_comment"]) {
        $query = "select * from $TABLE where id='{$input['id']}'";
        $result = my_query($query);
        $tags = array_merge($tags, $result->fetch_array());
        $tags['type'] = "edited_comment";
        $tags['form_title'] = "Редактирование";
        $tags['Header'] = "Редактирование комментария";
    } else {
        $tags['type'] = "added_comment";
        $tags['form_title'] = "Добавление";
        $tags['Header'] = "Добавление комментария";
    }
    $content.=get_tpl_by_name("comment_edit_form", $tags);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
    exit();
}

$query = "SELECT * from $TABLE order by id desc";
$result = my_query($query, true);

$tags['INCLUDE_HEAD'] = $JQUERY_INC;

$content.=get_tpl_by_name("comments_edit_table", $tags, $result);
echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
