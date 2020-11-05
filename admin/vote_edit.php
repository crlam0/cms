<?php

$tags['Header'] = "Голосования";
include "../include/common.php";

if ($input["view_vote"]) {
    $_SESSION["view_vote"] = $input["id"];
}

if ($input["list_vote"]) {
    $_SESSION["view_vote"] = "";
}

if ($input["del_variant"]) {
    $query = "delete from vote_variants where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('', [], "Вариант успешно удален.");
}

if ($input["added_variant"]) {
    $input['form'][vote_id] = $_SESSION["view_vote"];
    $query = "insert into vote_variants " . db_insert_fields($input['form']);
    my_query($query);
    $content.=my_msg_to_str('', [], "Вариант успешно добавлен.");
}

if ($input["edited_variant"]) {
    $input['form']['vote_id'] = $_SESSION["view_vote"];
    $query = "update vote_variants set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('', [], "Вариант успешно изменен.");
}

if (($input["edit_variant"]) || ($input["add_variant"])) {
    if ($_GET["edit_variant"]) {
	$query = "select * from vote_variants where id='{$input['id']}'";
	$result = my_query($query);
	$tags = array_merge($tags, $result->fetch_array());
	$tags['type'] = "edited_variant";
	$tags['form_title'] = "Редактирование";
    } else {
	$tags['type'] = "added_variant";
	$tags['form_title'] = "Добавление";
    }
    $content.=get_tpl_by_name("vote_variants_edit_form", $tags);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
    exit();
}

if ($_SESSION["view_vote"]) {
    $query = "SELECT vote_variants.*,count(vote_log.id) as hits from vote_variants
	left join vote_log on (vote_log.variant_id=vote_variants.id)
	where vote_id=" . $_SESSION["view_vote"] . " group by vote_variants.id order by num asc";
    $result = my_query($query);
    $content.=get_tpl_by_name("vote_variants_edit_table", $tags, $result);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
    exit();
}

if ($input["del_vote"]) {
    $query = "select id from vote_variants where vote_id='{$input['id']}'";
    $result = my_query($query);
    if ($result->num_rows) {
	$content.=my_msg_to_str('error', [],"Этот раздел не пустой !");
    } else {
        $query = "delete from vote_list where id='{$input['id']}'";
        my_query($query);
        $content.=my_msg_to_str('', [], "Голосование успешно удалено.");
    }
}

if ($input["added_vote"]) {
    if (!isset($input['form']['active'])
	)$input['form']['active'] = 0;
    $query = "insert into vote_list " . db_insert_fields($input['form']);
    my_query($query);
    $content.=my_msg_to_str('', [], "Вариант успешно добавлено.");
}

if ($input["edited_vote"]) {
    if (!isset($input['form']['active'])
	)$input['form']['active'] = 0;
    $query = "update vote_list set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('', [], "Вариант успешно изменено.");
}

if (($input["edit_vote"]) || ($input["add_vote"])) {
    if ($_GET["edit_vote"]) {
	$query = "select * from vote_list where id='{$input['id']}'";
	$result = my_query($query);
	$tags = array_merge($tags, $result->fetch_array());
	$tags['form_type'] = "edited_vote";
	$tags['form_title'] = "Редактирование";
    } else {
	$tags['form_type'] = "added_vote";
	$tags['form_title'] = "Добавление";
    }
    $tags['vote_type'] = "
	<option value=radio" . ($tags['type'] == "radio" ? " selected" : "") . ">1 из многих</option>
	<option value=checkbox" . ($tags['type'] == "checkbox" ? " selected" : "") . ">Несколько из многих</option>
	";
    if ($tags['active']
	)$tags['active'] = " checked";
    $content.=get_tpl_by_name("vote_list_edit_form", $tags);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
    exit();
}

$query = "SELECT vote_list.*,count(vote_variants.id) as variants
from vote_list 
left join vote_variants on (vote_variants.vote_id=vote_list.id) 
group by vote_list.id order by vote_list.title desc";
$result = my_query($query);

$content.=get_tpl_by_name("vote_list_edit_table", $tags, $result);
echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
