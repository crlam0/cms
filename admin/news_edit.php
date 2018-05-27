<?php

$tags[Header] = "Новости";
include "../include/common.php";

if ($input["del"]) {
    $query = "delete from news where id='$input[id]'";
    my_query($query, null, true);
    $content.=my_msg_to_str("", "", "Новость успешно удалена.");
}

if ($input["add"]) {
    $input[form][date] = "now()";
    $input[form][content] = replace_base_href($input[form][content], true);
    $query = "insert into news " . db_insert_fields($input[form]);
    my_query($query, null, true);
    $content.=my_msg_to_str("", "", "Новость успешно добавлена.");
}


if ($input["edit"]) {
    $input[form][date] = "now()";
    $input[form][content] = replace_base_href($input[form][content], true);
    $query = "update news set " . db_update_fields($input[form]) . " where id=" . $_POST["id"];
    my_query($query, null, true);
    $content.=my_msg_to_str("", "", "Новость успешно изменена.");
}

if (($input["view"]) || ($input["adding"])) {
    if ($_GET["view"]) {
        $query = "select * from news where id='$input[id]'";
        $result = my_query($query, $conn);
        $tags = array_merge($tags, $result->fetch_array());
        $tags[type] = "edit";
        $tags[form_title] = "Редактирование";
    } else {
        $tags[type] = "add";
        $tags[form_title] = "Добавление";
        $tags[date] = date("Y-m-d H:i:s");
    }
    $tags[content] = "<textarea id=editor name=form[content] rows=25 cols=120 maxlength=64000>$tags[content]</textarea>";
    $tags['INCLUDE_HEAD'] = $EDITOR_INC;
    $tags["content"] = replace_base_href($tags["content"], false);
    $content.=get_tpl_by_title("news_edit_form", $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
} else {
    $query = "SELECT * from news order by id desc";
    $result = my_query($query, $conn);
    $content.=get_tpl_by_title("news_edit_table", $tags, $result);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
}
?>
