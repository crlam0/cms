<?php

$tags['Header'] = "Мультимедиа";
include "../include/common.php";

if ($input["view_list"]) {
    $_SESSION["view_files"] = $input["id"];
}

if ($input["list_media"]) {
    $_SESSION["view_files"] = "";
}

if ($_SESSION["view_files"]) {
    list($list_title) = my_select_row("select title from media_list where id='" . $_SESSION["view_files"] . "'", 1);
    $tags['Header'].=" -> $list_title";
}

function show_size($tmp, $row) {
    global $DIR, $settings, $SUBDIR;
    $file_name = $settings["media_upload_path"] . $row['file_name'];
    if (is_file($DIR . $file_name)) {
	return "<a href=" . $SUBDIR . $file_name . "> Скачать ( " . convert_bytes(filesize($DIR . $file_name)) . " )</a>";
    } else {
	return "Отсутствует";
    }
}

if ($input["del_file"]) {
    list($fname_old) = my_select_row("select file_name from media_files where id='{$input['id']}'");
    if (is_file($DIR . $settings["media_upload_path"] . $fname_old)) {
	if (!unlink($DIR . $settings["media_upload_path"] . $fname_old)
	    )$content.=my_msg_to_str("error","","Ошибка удаления файла");
    }
    $query = "delete from media_files where id='{$input['id']}'";
    my_query($query, $conn);
    $content.=my_msg_to_str("", "", "Файл успешно удален.");
}

if ($input["added_file"]) {
    $input['form']['date_add'] = "now()";
    $input['form']['list_id'] = $_SESSION["view_files"];
    if ($_FILES["uploaded_file"]["size"] > 100) {
      	$f_info = pathinfo($_FILES["uploaded_file"]["name"]);
	$file_name = encodestring($f_info["filename"]) . "." . $f_info["extension"];

//	$file_name = str_replace(" ", "_", encodestring($_FILES["uploaded_file"]["name"]));
	if (move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $DIR . $settings["media_upload_path"] . $file_name)) {
	    $input['form'][file_name] = $file_name;
	} else {
	    $content.=my_msg_to_str("error","","Ошибка копирования файла !");
	}
    }

    $query = "insert into media_files " . db_insert_fields($input['form']);
    my_query($query, $conn);
    $content.=my_msg_to_str("", "", "Файл успешно добавлен.");
}

if ($input["edited_file"]) {
    if ($_FILES["uploaded_file"]["size"] > 100) {
	list($fname_old) = my_select_row("select file_name from media_files where id='{$input['id']}'");
	if (is_file($DIR . $settings["media_upload_path"] . $fname_old)) {
	    if (!unlink($DIR . $settings["media_upload_path"] . $fname_old)
		)$content.=my_msg_to_str("error","","Ошибка удаления файла");
	}
      	$f_info = pathinfo($_FILES["uploaded_file"]["name"]);
	$file_name = encodestring($f_info["filename"]) . "." . $f_info["extension"];
//	$file_name = str_replace(" ", "_", encodestring($_FILES["uploaded_file"]["name"]));
	if (move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $DIR . $settings["media_upload_path"] . $file_name)) {
	    $input['form'][file_name] = $file_name;
	} else {
	    $content.=my_msg_to_str("error","","Ошибка копирования файла !");
	}
    }
    $query = "update media_files set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query, $conn);
    $content.=my_msg_to_str("", "", "Файл успешно изменен.");
}

if (($input["edit_file"]) || ($input["add_file"])) {
    if ($_GET["edit_file"]) {
	$query = "select * from media_files where id='{$input['id']}'";
	$result = my_query($query, $conn);
	$tags = array_merge($tags, $result->fetch_array());
	$tags['type'] = "edited_file";
	$tags['form_title'] = "Редактирование";
    } else {
	$tags['type'] = "added_file";
	$tags['form_title'] = "Добавление";
    }
    $tags['descr'] = "<textarea class=\"form-control\" name=form[descr] rows=15 cols=100 maxlength=64000>${tags['descr']}</textarea>";
    $tags['INCLUDE_HEAD'] = $EDITOR_SIMPLE_INC;
    $content.=get_tpl_by_title("media_files_edit_form", $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit();
}

if ($_SESSION["view_files"]) {
    $query = "SELECT * from media_files where list_id=" . $_SESSION["view_files"] . " order by date_add asc";
    $result = my_query($query, $conn, true);
    $content.=get_tpl_by_title("media_files_edit_table", $tags, $result);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit();
}

if ($input["del_list"]) {
    $query = "select id from media_files where list_id='{$input['id']}'";
    $result = my_query($query, $conn);
    if ($result->num_rows) {
	$content.=my_msg_to_str("error","","Этот раздел не пустой !");
    } else {
	$query = "delete from media_list where id='{$input['id']}'";
	my_query($query, $conn);
	$content.=my_msg_to_str("", "", "Раздел успешно удален.");
    }
}

if ($input["added_list"]) {
    if (!strlen($input['form']['seo_alias']))$input['form']['seo_alias'] = encodestring($input['form']['title']);
    $input['form'][date_add] = "now()";
    $query = "insert into media_list " . db_insert_fields($input['form']);
    my_query($query, $conn);
    $content.=my_msg_to_str("", "", "Раздел успешно добавлен.");
}

if ($input["edited_list"]) {
    if (!strlen($input['form']['seo_alias']))$input['form']['seo_alias'] = encodestring($input['form']['title']);
    $query = "update media_list set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query, $conn);
    $content.=my_msg_to_str("", "", "Раздел успешно изменен.");
}

if (($input["edit_list"]) || ($input["add_list"])) {
    if ($_GET["edit_list"]) {
	$query = "select * from media_list where id='{$input['id']}'";
	$result = my_query($query, $conn);
	$tags = array_merge($tags, $result->fetch_array());
	$tags['type'] = "edited_list";
	$tags['form_title'] = "Редактирование";
    } else {
	$tags['type'] = "added_list";
	$tags['form_title'] = "Добавление";
    }
    $tags['descr'] = "<textarea class=\"form-control\" name=form[descr] rows=15 cols=80 maxlength=64000>{$tags['descr']}</textarea>";
    $tags['INCLUDE_HEAD'] = $EDITOR_SIMPLE_INC;
    $content.=get_tpl_by_title("media_list_edit_form", $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit();
}

$query = "SELECT media_list.*,count(media_files.id) as files
from media_list 
left join media_files on (media_files.list_id=media_list.id) 
group by media_list.id order by media_list.date_add desc";
$result = my_query($query, $conn, true);
$content.=get_tpl_by_title("media_list_edit_table", $tags, $result);
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);

