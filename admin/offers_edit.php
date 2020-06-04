<?php

$tags['Header'] = "Акции";
include "../include/common.php";

// print_array($settings);

$image_path = $settings['offers']['upload_path'];

function show_img($tmp, $row) {
    global $DIR, $image_path;
    if (is_file($DIR . $image_path . $row['file_name'])) {
        return "<img src=\"../".$image_path.$row['file_name']."\" border=0 width=200></a>";
    } else {
        return "Отсутствует";
    }
}

if ($input["del_offers"]) {
    list($img_old) = my_select_row("select file_name from offers where id='{$input['id']}'");
    if (is_file($DIR . $image_path . $img_old)) {
        if (!unlink($DIR . $image_path . $img_old)) {
            $content.=my_msg_to_str('error', [], "Ошибка удаления файла");
        }
    }
    $query = "delete from offers where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('', [], "Изображение успешно удалено.");
}

// ($_FILES["img_file"]);

if ($input["added_offers"]) {
    if (!strlen($input['form']['seo_alias'])){
        $input['form']['seo_alias'] = encodestring($input['form']['title']);
    }
    $input['form']['date'] = "now()";
    $input['form']['content'] = replace_base_href($input['form']['content'], true);
    $query = "insert into offers " . db_insert_fields($input['form']);
    my_query($query);
    if ($_FILES["img_file"]["size"] > 100) {
        if (!in_array($_FILES["img_file"]["type"], $validImageTypes)) {
            $content.=my_msg_to_str('error', [], "Неверный тип файла !");
        } else {
            $image_id = $mysqli->insert_id;
            $f_info = pathinfo($_FILES["img_file"]["name"]);
            $file_name = encodestring($input['form']['title']) . "." . $f_info["extension"];
            if (move_uploaded_image($_FILES["img_file"], $DIR . $image_path . $file_name, null, null, $settings['offers']['image_width'], $settings['offers']['image_height'])) {
                $query = "update offers set file_name='$file_name',file_type='" . $_FILES["img_file"]["type"] . "' where id='$image_id'";
                my_query($query);
                $content.=my_msg_to_str('', [], "Изображение успешно добавлено.");
            } else {
                $content.=my_msg_to_str('error', [], "Ошибка копирования файла !");
            }
        }
    }
}

if ($input["edited_offers"]) {
    if (!strlen($input['form']['seo_alias'])){
        $input['form']['seo_alias'] = encodestring($input['form']['title']);
    }
    $input['form']['date'] = "now()";
    $input['form']['content'] = replace_base_href($input['form']['content'], true);
    if ($_FILES["img_file"]["size"] > 100) {
        if (!in_array($_FILES["img_file"]["type"], $validImageTypes)) {
            $content.=my_msg_to_str('error', [], "Неверный тип файла !");
        } else {
            list($img_old) = my_select_row("select file_name from offers where id='{$input['id']}'");
            if (is_file($DIR . $image_path . $img_old)) {
                if (!unlink($DIR . $image_path . $img_old))
                    $content.=my_msg_to_str('error', [], "Ошибка удаления файла");
            }
            $f_info = pathinfo($_FILES["img_file"]["name"]);
            $file_name = encodestring($input['form']['title']) . "." . $f_info["extension"];
            if (move_uploaded_image($_FILES["img_file"], $DIR . $image_path . $file_name, null, null, $settings['offers']['image_width'], $settings['offers']['image_height'])) {
                $input['form']['file_name'] = $file_name;
                $input['form']['file_type'] = $_FILES["img_file"]["type"];
                $content.=my_msg_to_str('', [], "Изображение успешно изменено.");
            } else {
                $content.=my_msg_to_str('error', [], "Ошибка копирования файла !");
            }
        }
    }
    $query = "update offers set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
}

if (($input["edit_offers"]) || ($input["add_offers"])) {
    if ($input["edit_offers"]) {
        $query = "select * from offers where id='{$input['id']}'";
        $result = my_query($query);
        $tags = array_merge($tags, $result->fetch_array());
        $tags['type'] = "edited_offers";
        $tags['form_title'] = "Редактирование";
    } else {
        $tags['type'] = "added_offers";
        $tags['form_title'] = "Добавление";
        $tags['content'] = '';
    }
    $tags['content'] = "<textarea name=form[content] id=\"editor\" rows=15 cols=100 maxlength=64000>{$tags['content']}</textarea>";
    $tags['INCLUDE_HEAD'] = $JQUERY_INC . $EDITOR_INC;
    $tags['content'] = replace_base_href($tags['content'], false);    
    $content.=get_tpl_by_name("offers_edit_form", $tags);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
    exit();
}

$query = "SELECT * from offers order by date,title asc";
$result = my_query($query);
$content.=get_tpl_by_name("offers_edit_table", $tags, $result);

echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);

