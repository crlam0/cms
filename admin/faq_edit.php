<?php

$tags['Header'] = "Вопрос/ответ";
include "../include/common.php";

$TABLE = "faq";

if ($input["active"]) {
    $query = "update $TABLE set active='" . $input["active"] . "' where id=" . $input["id"];
    if (my_query($query, null, true)) {
        print $input["active"];
    } else {
        print mysql_error();
    }
    exit;
}

if ($input["del"]) {
    $query = "delete from $TABLE where id=" . $input["id"];
    $result = my_query($query, true);
    $list = 1;
    $content.=my_msg_to_str("", "", "Сообщение успешно удалено.");
}

if ($input["edited"]) {
    $query = "update $TABLE set " . db_update_fields($input['form']) . " where id=" . $input["edited"];
    $result = my_query($query, true);
    $list = 1;
    $content.=my_msg_to_str("", "", "Сообщение успешно изменено.");
}

if ($input["edit"]) {
    $query = "select * from $TABLE where id='{$input['id']}'";
    $result = my_query($query, true);
    $tags = array_merge($tags, $result->fetch_array());

//	$tags['INCLUDE_HEAD']=$EDITOR_SIMPLE_INC;

    $content.=get_tpl_by_title("faq_edit_form", $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit;
}

$query = "SELECT * from $TABLE order by id desc";
$result = my_query($query, true);

$tags['INCLUDE_HEAD'] = $JQUERY_INC;

$content.=get_tpl_by_title("faq_edit_table", $tags, $result);
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
