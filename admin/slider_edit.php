<?php

$tags[Header] = "Картинки для слайдера";
include "../include/common.php";

function show_img($tmp, $row) {
    global $DIR, $settings;
    if (is_file($DIR . $settings["slider_upload_path"] . $row[file_name])) {
        return "<img src=\"../".$settings["slider_upload_path"].$row[file_name]."\" border=0 width=200></a>";
    } else {
        return "Отсутствует";
    }
}

if ($input["del_image"]) {
    list($img_old) = my_select_row("select file_name from slider_image where id='$input[id]'");
    if (is_file($DIR . $settings["slider_upload_path"] . $img_old)) {
        if (!unlink($DIR . $settings["slider_upload_path"] . $img_old)
        )
            $content.=my_msg_to_str("error", "", "Ошибка удаления файла");
    }
    $query = "delete from slider_image where id='$input[id]'";
    my_query($query, $conn);
    $content.=my_msg_to_str("", "", "Фотография успешно удалена.");
}

// ($_FILES["img_file"]);

if ($input["added_image"]) {
    $query = "insert into slider_image " . db_insert_fields($input[form]);
    my_query($query, $conn);
    if ($_FILES["img_file"]["size"] > 100) {
        if (!in_array($_FILES["img_file"]["type"], $validImageTypes)) {
            $content.=my_msg_to_str("error", "", "Неверный тип файла !");
        } else {
            $f_info = pathinfo($_FILES["img_file"]["name"]);
            $file_name = encodestring($input[form][title]) . "." . $f_info["extension"];
            if (move_uploaded_image($_FILES["img_file"], $DIR . $settings["slider_upload_path"] . $file_name, 1600)) {
                $query = "update slider_image set file_name='$file_name',file_type='" . $_FILES["img_file"]["type"] . "' where id='$image_id'";
                my_query($query, $conn);
                $content.=my_msg_to_str("", "", "Фотография успешно добавлена.");
            } else {
                $content.=my_msg_to_str("error", "", "Ошибка копирования файла !");
            }
        }
    }
}

if ($input["edited_image"]) {
    if ($_FILES["img_file"]["size"] > 100) {
        if (!in_array($_FILES["img_file"]["type"], $validImageTypes)) {
            $content.=my_msg_to_str("error", "", "Неверный тип файла !");
        } else {
            list($img_old) = my_select_row("select file_name from slider_image where id='$input[id]'");
            if (is_file($DIR . $settings["slider_upload_path"] . $img_old)) {
                if (!unlink($DIR . $settings["slider_upload_path"] . $img_old))
                    $content.=my_msg_to_str("error", "", "Ошибка удаления файла");
            }
            $f_info = pathinfo($_FILES["img_file"]["name"]);
            $file_name = encodestring($input[form][title]) . "." . $f_info["extension"];
            if (move_uploaded_image($_FILES["img_file"], $DIR . $settings["slider_upload_path"] . $file_name, 1600)) {
                $input[form][file_name] = $file_name;
                $input[form][file_type] = $_FILES["img_file"]["type"];
                $content.=my_msg_to_str("", "", "Фотография успешно изменена.");
            } else {
                $content.=my_msg_to_str("error", "", "Ошибка копирования файла !");
            }
        }
    }
    $query = "update slider_image set " . db_update_fields($input[form]) . " where id='$input[id]'";
    my_query($query, $conn);
}

if (($input["edit_image"]) || ($input["add_image"])) {
    if ($_GET["edit_image"]) {
        $query = "select * from slider_image where id='$input[id]'";
        $result = my_query($query, $conn);
        $tags = array_merge($tags, $result->fetch_array());
        $tags[type] = "edited_image";
        $tags[form_title] = "Редактирование";
    } else {
        $tags[type] = "added_image";
        $tags[form_title] = "Добавление";
    }
    $tags[descr] = "<textarea name=form[descr] rows=15 cols=100 maxlength=64000>$tags[descr]</textarea>";
    $tags[head_inc] = $EDITOR_SIMPLE_INC;
    $content.=get_tpl_by_title("slider_image_edit_form", $tags);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit();
}

$query = "SELECT * from slider_image order by pos,title asc";
$result = my_query($query, $conn, true);
$content.=get_tpl_by_title("slider_image_edit_table", $tags, $result);
echo get_tpl_by_title($part[tpl_name], $tags, "", $content);

?>
