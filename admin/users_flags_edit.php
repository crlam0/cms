<?php

$tags[Header] = "Флаги пользователей";
include "../include/common.php";

if ($input["del"]) {
    $query = "delete from users_flags where id='$input[id]'";
    my_query($query, $conn, 1);
}

if ($input["add"]) {
    $query = "insert into users_flags " . db_insert_fields($input[form]);
    my_query($query, $conn, 1);
}


if ($input["edit"]) {
    $query = "update users_flags set " . db_update_fields($input[form]) . " where id='$input[id]'";
    my_query($query, $conn, 1);
}

if (($input["view"]) || ($input["adding"])) {
    if ($input["view"]) {
	$query = "select * from users_flags where id='$input[id]'";
	$result = my_query($query, $conn);
	$tags = array_merge($tags, $result->fetch_array());
	$tags[type] = "edit";
	$tags[form_title] = "Редактирование";
    } else {
	$tags[type] = "add";
	$tags[form_title] = "Добавление";
    }
    $content.=get_tpl_by_title("users_flags_edit_form", $tags);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
} else {

    $query = "SELECT * from users_flags order by title asc";
    $result = my_query($query, $conn);
    $content.=get_tpl_by_title("users_flags_edit_table", $tags, $result);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
}
?>
