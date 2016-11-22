<?php

$tags[Header] = "Разделы сайта";
include "../include/common.php";

if ($input["del"]) {
    $query = "delete from parts where id='$input[id]'";
    my_query($query, null, true);
}

if ($input["add"]) {
    $query = "insert into parts " . db_insert_fields($input[form]);
    my_query($query, null, true);
}


if ($input["edit"]) {
    $query = "update parts set " . db_update_fields($input[form]) . " where id='$input[id]'";
    my_query($query, null, true);
}

if (($input["view"]) || ($input["adding"])) {
    if ($input["view"]) {
	$query = "select * from parts where id='$input[id]'";
	$result = my_query($query, $conn);
	$tags = array_merge($tags, $result->fetch_array());
	$tags[type] = "edit";
	$tags[form_title] = "Редактирование";
    } else {
	$tags[type] = "add";
	$tags[form_title] = "Добавление";
    }
    $tags[user_flags] = "";
    $query = "SELECT * from users_flags order by title asc";
    $result = my_query($query, $conn);
    while ($row = $result->fetch_array()) {
	$tags[user_flags].="<option value=\"$row[value]\"" . ($tags[user_flag] == $row[value] ? " selected" : "") . ">$row[title]</option>";
    }
    $content.=get_tpl_by_title("parts_edit_form", $tags);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
} else {

    $query = "SELECT * from parts order by title asc";
    $result = my_query($query, $conn);
    $content.=get_tpl_by_title("parts_edit_table", $tags, $result);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
}
?>
