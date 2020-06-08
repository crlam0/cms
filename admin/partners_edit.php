<?php

$tags['Header'] = "Партнеры";
include "../include/common.php";

function show_img($tmp, $row) {
    global $DIR, $settings;
    if (is_file($DIR . $settings['partners']['upload_path'] . $row['file_name'])) {
        return "<img src=\"../".$settings['partners']['upload_path'].$row['file_name']."\" border=0 width=200></a>";
    } else {
        return "Отсутствует";
    }
}

if ($input["del_partner"]) {
    list($img_old) = my_select_row("select file_name from partners where id='{$input['id']}'");
    if (is_file($DIR . $settings['partners']['upload_path'] . $img_old)) {
        if (!unlink($DIR . $settings['partners']['upload_path'] . $img_old)
        )
            $content.=my_msg_to_str('error', [], "Ошибка удаления файла");
    }
    $query = "delete from partners where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('', [], "Картинка успешно удалена.");
}

// ($_FILES["img_file"]);

if ($input["added_partner"]) {
    $query = "insert into partners " . db_insert_fields($input['form']);
    my_query($query);
    if ($_FILES["img_file"]["size"] > 100) {
        if (!in_array($_FILES["img_file"]["type"], $validImageTypes)) {
            $content.=my_msg_to_str('error', [], "Неверный тип файла !");
        } else {
            $image_id = $mysqli->insert_id;
            $f_info = pathinfo($_FILES["img_file"]["name"]);
            $file_name = encodestring($input['form']['title']) . "." . $f_info["extension"];
            if (move_uploaded_image($_FILES["img_file"], $DIR . $settings['partners']['upload_path'] . $file_name, $settings['partners']['image_width'])) {
                $query = "update partners set file_name='$file_name',file_type='" . $_FILES["img_file"]["type"] . "' where id='$image_id'";
                my_query($query);
                $content.=my_msg_to_str('', [], "Картинка успешно добавлена.");
            } else {
                $content.=my_msg_to_str('error', [], "Ошибка копирования файла !");
            }
        }
    }
}

if ($input["edited_partner"]) {
    if ($_FILES["img_file"]["size"] > 100) {
        if (!in_array($_FILES["img_file"]["type"], $validImageTypes)) {
            $content.=my_msg_to_str('error', [], "Неверный тип файла !");
        } else {
            list($img_old) = my_select_row("select file_name from partners where id='{$input['id']}'");
            if (is_file($DIR . $settings['partners']['upload_path'] . $img_old)) {
                if (!unlink($DIR . $settings['partners']['upload_path'] . $img_old))
                    $content.=my_msg_to_str('error', [], "Ошибка удаления файла");
            }
            $f_info = pathinfo($_FILES["img_file"]["name"]);
            $file_name = encodestring($input['form']['title']) . "." . $f_info["extension"];
            if (move_uploaded_image($_FILES["img_file"], $DIR . $settings['partners']['upload_path'] . $file_name, $settings['partners']['image_width'])) {
                $input['form']['file_name'] = $file_name;
                $input['form']['file_type'] = $_FILES["img_file"]["type"];
                $content.=my_msg_to_str('', [], "Картинка успешно изменена.");
            } else {
                $content.=my_msg_to_str('error', [], "Ошибка копирования файла !");
            }
        }
    }
    $query = "update partners set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
}

if (($input["edit_partner"]) || ($input["add_partner"])) {
    if ($_GET["edit_partner"]) {
        $query = "select * from partners where id='{$input['id']}'";
        $result = my_query($query);
        $tags = array_merge($tags, $result->fetch_array());
        $tags['type'] = "edited_partner";
        $tags['form_title'] = "Редактирование";
    } else {
        $tags['type'] = "added_partner";
        $tags['form_title'] = "Добавление";
    }
    $tags['descr'] = "<textarea name=form[descr] rows=15 cols=100 maxlength=64000>$tags[descr]</textarea>";
    $tags['head_inc'] = $EDITOR_SIMPLE_INC;
    $content.=get_tpl_by_name("partners_edit_form", $tags);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
    exit();
}

$query = "SELECT * from partners order by pos,title asc";
$result = my_query($query);
$content.=get_tpl_by_name("partners_edit_table", $tags, $result);
echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);

