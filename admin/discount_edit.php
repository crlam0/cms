<?php

$tags["Header"] = "Скидки";
include "../include/common.php";

if ($input["del"]) {
    App::$db->deleteFromTable('discount', ['id' => $input['id']]);
}

if ($input["add"]) {
    App::$db->insertTable('discount', $input['form']);
}


if ($input["edit"]) {
    App::$db->updateTable('discount', $input['form'], ['id' => $input['id']]);
}

$content = '';

if (($input["view"]) || ($input["adding"])) {
    if ($input["view"]) {
        $query = "select * from discount where id=?";
        $result = App::$db->query($query, ['id' => $input['id']]);
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
    $content .= App::$template->parse("discount_edit_form", $tags);
} else {
    $query = "SELECT * from discount order by summ asc";
    $result = App::$db->query($query);

    $content.=App::$template->parse("discount_edit_table", $tags, $result);
}
echo App::$template->parse($part['tpl_name'], $tags, null, $content);
