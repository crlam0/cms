<?php
$tags['Header'] = 'Картинки для слайдера';
include '../include/common.php';

use Classes\App;

$image_path = $settings['slider']['upload_path'];

function show_img($tmp, $row) {
    global $image_path;
    if (is_file(App::$DIR . $image_path . $row['file_name'])) {
        return "<img src=\"../".$image_path.$row['file_name']."\" border=0 width=200></a>";
    } else {
        return "Отсутствует";
    }
}

if ($input["del_image"]) {
    list($img_old) = my_select_row("select file_name from slider_images where id='{$input['id']}'");
    if (is_file($DIR . $image_path . $img_old)) {
        if (!unlink($DIR . $image_path . $img_old)
        )
            $content.=my_msg_to_str('error', [], "Ошибка удаления файла");
    }
    $query = "delete from slider_images where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('', [], "Изображение успешно удалено.");
}

if ($input["added_image"]) {
    $query = "insert into slider_images " . db_insert_fields($input['form']);
    my_query($query);
    if ($_FILES["img_file"]["size"] > 100) {
        if (!in_array($_FILES["img_file"]["type"], $validImageTypes)) {
            $content.=my_msg_to_str('error', [], "Неверный тип файла !");
        } else {
            $image_id = App::$db->insert_id();
            $f_info = pathinfo($_FILES["img_file"]["name"]);
            $file_name = encodestring($input['form']['title']) . "." . $f_info["extension"];
            if (move_uploaded_image($_FILES["img_file"], $DIR . $image_path . $file_name, null, null, $settings['slider']['image_width'], $settings['slider']['image_height'])) {
                $query = "update slider_images set file_name='$file_name',file_type='" . $_FILES["img_file"]["type"] . "' where id='$image_id'";
                my_query($query);
                $content.=my_msg_to_str('', [], "Изображение успешно добавлено.");
            } else {
                $content.=my_msg_to_str('error', [], "Ошибка копирования файла !");
            }
        }
    }
}

if ($input["edited_image"]) {
    if ($_FILES["img_file"]["size"] > 100) {
        if (!in_array($_FILES["img_file"]["type"], $validImageTypes)) {
            $content.=my_msg_to_str('error', [], "Неверный тип файла !");
        } else {
            list($img_old) = my_select_row("select file_name from slider_images where id='{$input['id']}'");
            if (is_file($DIR . $image_path . $img_old)) {
                if (!unlink($DIR . $image_path . $img_old))
                    $content.=my_msg_to_str('error', [], "Ошибка удаления файла");
            }
            $f_info = pathinfo($_FILES["img_file"]["name"]);
            $file_name = encodestring($input['form']['title']) . "." . $f_info["extension"];
            if (move_uploaded_image($_FILES["img_file"], $DIR . $image_path . $file_name, null, null, $settings['slider']['image_width'], $settings['slider']['image_height'])) {
                $input['form']['file_name'] = $file_name;
                $input['form']['file_type'] = $_FILES["img_file"]["type"];
                $content.=my_msg_to_str('', [], "Изображение успешно изменено.");
            } else {
                $content.=my_msg_to_str('error', [], "Ошибка копирования файла !");
            }
        }
    }
    $query = "update slider_images set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
}

if (($input['edit_image']) || ($input['add_image'])) {
    if ($input['edit_image']) {
        $query = "select * from slider_images where id=?";
        $result = App::$db->query($query, ['id' => $input['id']]);
        $tags = array_merge($tags, $result->fetch_assoc());
        $tags['type'] = 'edited_image';
        $tags['form_title'] = 'Редактирование';
    } else {
        $tags['type'] = 'added_image';
        $tags['form_title'] = 'Добавление';
        $tags['descr'] = '';
    }
    $tags['descr'] = "<textarea name=form[descr] rows=15 cols=100 maxlength=64000>{$tags['descr']}</textarea>";
    $content .= App::$template->parse('slider_images_edit_form', $tags);
    echo get_tpl_default($tags, '', $content);
    exit();
}

$query = "SELECT * from slider_images order by pos,title asc";
$result = my_query($query);
$content .= App::$template->parse('slider_images_edit_table', $tags, $result);

echo get_tpl_default($tags, '', $content);

