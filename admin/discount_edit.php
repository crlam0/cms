<?php

$tags["Header"] = "Скидки";
include "../include/common.php";

if ($input["del"]) {
    $query = "delete from discount where id=" . $input["id"];
    my_query($query);
}

if ($input["add"]) {
    $query = "insert into discount " . db_insert_fields($input['form']);
    my_query($query);
}


if ($input["edit"]) {
    $query = "update discount set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
}

if (($input["view"]) || ($input["adding"])) {
    if ($input["view"]) {
        $query = "select * from discount where id=" . $input["id"];
        $result = my_query($query);
        $tags=array_merge($tags, $result->fetch_array());
        $tags['form_title'] = "Редактирование";
        $tags['type'] = "edit";
        $tags['Header'] = "Редактирование скидки";
    } else {
        $tags['form_title'] = "Добавление";
        $tags['type'] = "add";
        $tags['Header'] = "Добавление скидки";
    }
    echo $tags['summ'];
    $content .= get_tpl_by_name("discount_edit_form", $tags);
} else {
    $query = "SELECT * from discount order by summ asc";
    $result = my_query($query);

    $content.=get_tpl_by_name("discount_edit_table", $tags, $result);
}
echo get_tpl_by_name($part['tpl_name'], $tags, null, $content);
