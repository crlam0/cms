<?php

$tags['Header'] = "Комментарии";
include "../include/common.php";

use classes\App;

$TABLE = "comments";

if ($input["active"]) {
    $query = "update $TABLE set active=? where id=?";
    if (App::$db->query($query, ['active' => $input['active'], 'id' => $input['id']])) {
        echo $input["active"];
    } else {
        echo mysql_error();
    }
    exit;
}

if ($input["del_comment"]) {
    App::$db->deleteFromTable($TABLE, ['id' => $input['id']]);
    $list = 1;
    $content.=App::$message->get('', [], "Комментарий успешно удален.");
}

if ($input["edited_comment"]) {
    App::$db->updateTable($TABLE, $input['form'], ['id' => $input['id']]);
    $content.=App::$message->get('', [], "Комментарий успешно изменен.");
}

if (($input["edit_comment"]) || ($input["add_comment"])) {
    if ($input["edit_comment"]) {
        $query = "select * from $TABLE where id=?";
        $result = App::$db->query($query, ['id' => $input['id']]);
        $tags = array_merge($tags, $result->fetch_array());
        $tags['type'] = "edited_comment";
        $tags['form_title'] = "Редактирование";
        $tags['Header'] = "Редактирование комментария";
    } else {
        $tags['type'] = "added_comment";
        $tags['form_title'] = "Добавление";
        $tags['Header'] = "Добавление комментария";
    }
    $content.=App::$template->parse("comment_edit_form", $tags);
    echo App::$template->parse($part['tpl_name'], $tags, null, $content);
    exit();
}

$query = "SELECT * from $TABLE order by id desc";
$result = App::$db->query($query);

$tags['INCLUDE_HEAD'] = $JQUERY_INC;

$content.=App::$template->parse("comments_edit_table", $tags, $result);
echo App::$template->parse($part['tpl_name'], $tags, null, $content);
